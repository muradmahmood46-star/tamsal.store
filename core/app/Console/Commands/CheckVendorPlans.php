<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Helpers\PriceHelper;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\VendorNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckVendorPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:check-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate vendor time-based subscription plans, process auto-renewals, apply plan expiry, and send 3-day reminder notifications.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            Helper::ensureStoreTables();
        } catch (\Throwable $e) {}

        $setting = Setting::first();
        if (!$setting || $setting->vendor_plan_mode !== 'time_based') {
            $this->info('Vendor system is currently in Commission Per Order mode. No time-based plan evaluation needed.');
            return Command::SUCCESS;
        }

        $sellers = Seller::where('status', 1)->get();
        $this->info("Evaluating " . $sellers->count() . " active sellers for Time-Based Plan rules...");

        $now = Carbon::now();
        $freeDays = (int)($setting->vendor_free_days ?? 30);
        if ($freeDays < 1) {
            $freeDays = 30;
        }
        $planCharge = (float)($setting->vendor_plan_charge ?? 1000.00);

        $renewedCount = 0;
        $expiredCount = 0;
        $remindedCount = 0;

        foreach ($sellers as $seller) {
            // Case 1: Seller has no plan dates initialized
            if (!$seller->plan_end_date) {
                $seller->plan_status = 'free_time';
                $seller->plan_start_date = $now;
                $seller->plan_end_date = $now->copy()->addDays($freeDays);
                $seller->plan_warned_at = null;
                $seller->save();
                $this->line("Store [{$seller->shop_name}] initialized with {$freeDays} days Free Time.");
                continue;
            }

            $endDate = Carbon::parse($seller->plan_end_date);

            // Case 2: Plan is expired or due today
            if ($endDate->lte($now)) {
                $res = $seller->checkAndRenewPlan();
                if (($res['status'] ?? '') === 'renewed') {
                    $renewedCount++;
                    $this->info("Store [{$seller->shop_name}] auto-renewed successfully until " . $seller->plan_end_date->format('d M Y'));
                } else {
                    $expiredCount++;
                    $this->warn("Store [{$seller->shop_name}] plan expired due to insufficient wallet balance.");
                }
                continue;
            }

            // Case 3: Plan is active — Check 3-day low balance reminder
            $daysLeft = $seller->plan_days_remaining;
            if ($daysLeft <= 3 && $daysLeft >= 0) {
                if ((float)$seller->balance < $planCharge) {
                    $shouldWarn = false;
                    if ($seller->plan_warned_at === null) {
                        $shouldWarn = true;
                    } elseif (Carbon::parse($seller->plan_warned_at)->lt($now->copy()->subDays(2))) {
                        $shouldWarn = true;
                    }

                    if ($shouldWarn) {
                        VendorNotification::log(
                            $seller->user_id,
                            'plan_renewal_reminder',
                            __('Store Plan Expiring Soon (Low Balance)'),
                            __('Your store plan will end in :days day(s) on :date. Your current wallet balance is :curr :balance, which is below the renewal charge of :curr :charge. Please top up your wallet to avoid store disruption.', [
                                'days' => $daysLeft,
                                'date' => $endDate->format('d M Y'),
                                'curr' => PriceHelper::adminCurrency(),
                                'balance' => number_format((float)$seller->balance, 2),
                                'charge' => number_format($planCharge, 2)
                            ]),
                            route('seller.wallet.index'),
                            'fas fa-clock',
                            'warning'
                        );

                        $seller->plan_warned_at = $now;
                        $seller->save();
                        $remindedCount++;
                        $this->line("Store [{$seller->shop_name}] sent 3-day renewal reminder notification.");
                    }
                }
            }
        }

        $this->info("Evaluation complete: Renewed: {$renewedCount}, Expired: {$expiredCount}, Reminded: {$remindedCount}");
        return Command::SUCCESS;
    }
}
