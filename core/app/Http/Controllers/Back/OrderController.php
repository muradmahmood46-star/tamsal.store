<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\Order,
    Models\PromoCode,
    Models\TrackOrder,
    Http\Controllers\Controller
};
use App\Helpers\EmailHelper;
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Helpers\WhatsAppHelper;
use App\Jobs\EmailSendJob;
use App\Models\Notification;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = Order::latest('id');

        if ($type) {
            $query->where('order_status', $type);

            // New Orders (Pending) should ONLY show Admin's own orders (not vendor store orders)
            if ($type == 'Pending') {
                Notification::whereNotNull('order_id')->where('is_read', 0)->whereHas('order', function($q) {
                    $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
                })->update(['is_read' => 1]);

                $query->where(function($q) {
                    $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
                });
            }
        }

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $query->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        }

        $datas = $query->get();

        return view('back.order.index', compact('datas'));
    }

    
    public function edit($id)
    {
        
        $order = Order::findOrFail($id);
        return view('back.order.edit', compact('order'));
    }

    

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Check if order_id is available
        if (Order::where('transaction_number', $request->transaction_number)->where('id', '!=', $id)->exists()) {
            return redirect()->route('back.order.index')->withErrors(__('Order ID already exists.'));
        }

        $old_order_status = $order->order_status;
        $old_payment_status = $order->payment_status;

        $order->update($request->all());

        if ($request->filled('order_status') && $request->order_status != $old_order_status) {
            $this->setTrackOrder($order);
            $this->sendOrderStatusUpdateMail($order, $request->order_status);
        }

        if ($request->filled('payment_status') && $request->payment_status == 'Paid' && $old_payment_status != 'Paid') {
            $this->setPromoCode($order);
            $this->sendPaymentAcceptedMail($order);
        }

        return redirect()->route('back.order.index')->withSuccess(__('Order Updated Successfully.'));
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        Notification::where('order_id', $id)->where('is_read', 0)->update(['is_read' => 1]);
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
        return view('back.order.invoice',compact('order','cart'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printOrder($id)
    {
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
        return view('back.order.print',compact('order','cart'));
    }


    /**
     * Change the status for editing the specified resource.
     *
     * @param  int  $id
     * @param  string  $field
     * @param  string  $value
     * @return \Illuminate\Http\Response
     */
    public function status($id,$field,$value)
    {

        $order = Order::find($id);
        if($field == 'payment_status'){
            if($order['payment_status'] == 'Paid' && $value == 'Paid'){
                return redirect()->back()->withErrors(__('Order is already paid.'));
            }
        }
        if($field == 'order_status'){
            if($order['order_status'] == 'Delivered' && $value == 'Delivered'){
                return redirect()->back()->withErrors(__('Order is already Delivered.'));
            }
        }
        $order->update([$field => $value]);
        if($field == 'payment_status' && $order->payment_status == 'Paid'){
            $this->setPromoCode($order);
            $this->sendPaymentAcceptedMail($order);
            // WhatsApp: Order Confirmed (Payment Accepted)
            try { WhatsAppHelper::sendOrderConfirmed($order); } catch (\Throwable $e) {}
        }
        if($field == 'order_status'){
            $this->sendOrderStatusUpdateMail($order, $value);
            // WhatsApp: status-specific messages
            try {
                if ($value == 'In Progress')  { WhatsAppHelper::sendOrderInProgress($order); }
                elseif ($value == 'Delivered') { WhatsAppHelper::sendOrderDelivered($order); }
                elseif ($value == 'Canceled')  { WhatsAppHelper::sendOrderCanceled($order); }
            } catch (\Throwable $e) {}
        }
        $this->setTrackOrder($order);
        
        $sms = new SmsHelper();
        $user_number = $order->user->phone ?? (json_decode($order->billing_info, true)['bill_phone'] ?? null);
        if($user_number){
            $sms->SendSms($user_number,"'order_status'",$order->transaction_number);
        }
       
        return redirect()->back()->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Custom Function
     */
    public function setTrackOrder($order)
    {
        if ($order->order_status == 'Accepted') {
            TrackOrder::addTrack($order, 'Accepted');
        } elseif ($order->order_status == 'Send to Delivery House') {
            TrackOrder::addTrack($order, 'Accepted');
            TrackOrder::addTrack($order, 'Send to Delivery House');
        } elseif ($order->order_status == 'In Progress') {
            TrackOrder::addTrack($order, 'Accepted');
            TrackOrder::addTrack($order, 'Send to Delivery House');
            TrackOrder::addTrack($order, 'In Progress');
        } elseif ($order->order_status == 'Delivered') {
            TrackOrder::addTrack($order, 'Accepted');
            TrackOrder::addTrack($order, 'Send to Delivery House');
            TrackOrder::addTrack($order, 'In Progress');
            TrackOrder::addTrack($order, 'Delivered');
        } elseif ($order->order_status == 'Canceled') {
            TrackOrder::addTrack($order, 'Canceled');
        }
    }


    public function setPromoCode($order)
    {

        $discount = json_decode($order->discount, true);
        if($discount != null){
            $code = PromoCode::find($discount['code']['id']);
            $code->no_of_times--;
            $code->update();
        }
    }


    public function delete($id)
    {
        $order = Order::findOrFail($id);
        $order->tranaction->delete();
        if(Notification::where('order_id',$id)->exists()){
            Notification::where('order_id',$id)->delete();
        }
        if(count($order->tracks_data)>0){
            foreach($order->tracks_data as $track){
                $track->delete();
            }
        }
        $order->delete();
        return redirect()->back()->withSuccess(__('Order Deleted Successfully.'));
    }

    public function sendPaymentAcceptedMail($order)
    {
        $billing_info = json_decode($order->billing_info, true);
        $user_email = $order->user->email ?? ($billing_info['bill_email'] ?? null);
        $user_name = $order->user->name ?? trim(($billing_info['bill_first_name'] ?? '') . ' ' . ($billing_info['bill_last_name'] ?? ''));
        if (!$user_name) {
            $user_name = $order->account_name ?: 'Valued Customer';
        }

        if (!$user_email) {
            return;
        }

        $setting = Setting::first();
        $site_title = $setting ? $setting->title : 'Namartzone';
        $total_amount = ($setting->currency_direction == 1 ? $order->currency_sign : '') . PriceHelper::OrderTotal($order) . ($setting->currency_direction != 1 ? $order->currency_sign : '');

        $email_subject = "Order #{$order->transaction_number} - Payment Accepted & Verified";

        $email_body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 25px; border: 1px solid #e5e7eb; border-radius: 10px; background-color: #ffffff; color: #333333;">
            <div style="text-align: center; border-bottom: 2px solid #28a745; padding-bottom: 15px; margin-bottom: 20px;">
                <h2 style="color: #28a745; margin: 0 0 5px 0;">Order & Payment Accepted!</h2>
                <p style="color: #6b7280; font-size: 14px; margin: 0;">Thank you for your purchase with ' . htmlspecialchars($site_title) . '</p>
            </div>
            
            <p style="font-size: 15px;">Assalam-o-Alaikum / Dear <strong>' . htmlspecialchars($user_name) . '</strong>,</p>
            <p style="font-size: 14px; line-height: 1.6;">We are pleased to inform you that your payment for Order <strong>#' . htmlspecialchars($order->transaction_number) . '</strong> has been <strong>successfully verified and accepted</strong> by our team.</p>
            
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 18px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin: 0 0 12px 0; color: #1e293b; font-size: 16px; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px;">Order Details:</h4>
                <table style="width: 100%; font-size: 14px; line-height: 1.8;">
                    <tr>
                        <td style="color: #64748b; width: 40%;"><strong>Order ID:</strong></td>
                        <td style="color: #0f172a; font-weight: bold;">' . htmlspecialchars($order->transaction_number) . '</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;"><strong>Payment Method:</strong></td>
                        <td style="color: #0f172a;">' . htmlspecialchars($order->payment_method) . '</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;"><strong>Payment Status:</strong></td>
                        <td><span style="color: #ffffff; background-color: #28a745; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">PAID</span></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;"><strong>Total Amount:</strong></td>
                        <td style="color: #6f42c1; font-weight: bold; font-size: 15px;">' . htmlspecialchars($total_amount) . '</td>
                    </tr>
                </table>
            </div>
            
            <p style="font-size: 14px; line-height: 1.6;">Your order is now being processed for packing and delivery. You will receive further updates as your package is dispatched.</p>
            
            <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 12px;">
                <p style="margin: 4px 0;">If you have any questions, feel free to contact our customer support.</p>
                <p style="margin: 4px 0;">&copy; ' . date('Y') . ' ' . htmlspecialchars($site_title) . '. All rights reserved.</p>
            </div>
        </div>';

        $emailData = [
            'to' => $user_email,
            'subject' => $email_subject,
            'body' => $email_body,
        ];

        try {
            if ($setting && $setting->is_queue_enabled == 1) {
                dispatch(new EmailSendJob($emailData, "custom"));
            } else {
                $emailHelper = new EmailHelper();
                $emailHelper->sendCustomMail($emailData);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment accepted email error: ' . $e->getMessage());
        }
    }

    /**
     * Send automatic email notification to customer on order status changes
     */
    public function sendOrderStatusUpdateMail($order, $status)
    {
        $billing_info = json_decode($order->billing_info, true) ?: [];
        $shipping_info = json_decode($order->shipping_info, true) ?: [];
        $user_email = $order->user->email ?? ($billing_info['bill_email'] ?? ($shipping_info['ship_email'] ?? null));
        $user_name = $order->user->name ?? trim(($billing_info['bill_first_name'] ?? '') . ' ' . ($billing_info['bill_last_name'] ?? ''));
        if (!$user_name) {
            $user_name = $order->account_name ?: 'Valued Customer';
        }

        if (!$user_email) {
            return;
        }

        $setting = Setting::first();
        $site_title = $setting ? $setting->title : 'Namartzone';
        $total_amount = ($setting->currency_direction == 1 ? $order->currency_sign : '') . PriceHelper::OrderTotal($order) . ($setting->currency_direction != 1 ? $order->currency_sign : '');
        $track_url = url('/order/track/submit?order_number=' . $order->transaction_number);

        // Status configurations
        if ($status == 'In Progress') {
            $status_title = 'Order Confirmed & Delivery In Progress';
            $status_badge = 'IN PROGRESS / DISPATCHED';
            $badge_bg = '#0284c7';
            $status_msg = 'Great news! Your order <strong>#' . htmlspecialchars($order->transaction_number) . '</strong> has been confirmed and is now being packed / dispatched for delivery.';
            $subject = "Order #{$order->transaction_number} Confirmed & In Progress - {$site_title}";
        } elseif ($status == 'Delivered') {
            $status_title = 'Order Delivered Successfully! 🎉';
            $status_badge = 'DELIVERED';
            $badge_bg = '#16a34a';
            $status_msg = 'Your order <strong>#' . htmlspecialchars($order->transaction_number) . '</strong> has been successfully delivered! We hope you love your products.';
            $subject = "Order #{$order->transaction_number} Delivered! Thank you for shopping with {$site_title}";
        } elseif ($status == 'Canceled') {
            $status_title = 'Order Cancellation Notice';
            $status_badge = 'CANCELED';
            $badge_bg = '#dc2626';
            $status_msg = 'Your order <strong>#' . htmlspecialchars($order->transaction_number) . '</strong> has been canceled. If you have any questions or this was an error, please contact our support team.';
            $subject = "Order #{$order->transaction_number} Cancellation Update - {$site_title}";
        } else {
            $status_title = 'Order Status Update';
            $status_badge = strtoupper($status);
            $badge_bg = '#f59e0b';
            $status_msg = 'The status of your order <strong>#' . htmlspecialchars($order->transaction_number) . '</strong> has been updated to: <strong>' . htmlspecialchars($status) . '</strong>.';
            $subject = "Order #{$order->transaction_number} Status Update - {$site_title}";
        }

        // Cart items html list
        $cart = json_decode($order->cart, true) ?: [];
        $items_rows = '';
        foreach ($cart as $cart_item) {
            $itemName = htmlspecialchars($cart_item['name'] ?? 'Product');
            $itemQty = $cart_item['qty'] ?? 1;
            $itemPrice = ($setting->currency_direction == 1 ? $order->currency_sign : '') . PriceHelper::setConvertPrice(($cart_item['main_price'] ?? 0) * $itemQty) . ($setting->currency_direction != 1 ? $order->currency_sign : '');

            $variantInfo = '';
            if (!empty($cart_item['options']['color']) || !empty($cart_item['options']['size'])) {
                $variantParts = [];
                if (!empty($cart_item['options']['color'])) $variantParts[] = 'Color: ' . $cart_item['options']['color'];
                if (!empty($cart_item['options']['size'])) $variantParts[] = 'Size: ' . $cart_item['options']['size'];
                $variantInfo = '<div style="font-size: 12px; color: #64748b; margin-top: 2px;">' . htmlspecialchars(implode(' | ', $variantParts)) . '</div>';
            } elseif (!empty($cart_item['attribute']['names'])) {
                $variantInfo = '<div style="font-size: 12px; color: #64748b; margin-top: 2px;">' . htmlspecialchars(implode(', ', $cart_item['attribute']['names'])) . '</div>';
            }

            $items_rows .= '
            <tr style="border-bottom: 1px solid #e2e8f0;">
                <td style="padding: 10px 0;">
                    <div style="font-weight: 600; color: #1e293b; font-size: 14px;">' . $itemName . '</div>
                    ' . $variantInfo . '
                </td>
                <td style="padding: 10px 0; text-align: center; color: #475569; font-size: 13px;">x' . $itemQty . '</td>
                <td style="padding: 10px 0; text-align: right; font-weight: 600; color: #0f172a; font-size: 14px;">' . $itemPrice . '</td>
            </tr>';
        }

        // Shipping address string
        $shipping_address_str = '';
        if (!empty($shipping_info['ship_address1'])) {
            $shipping_address_str = htmlspecialchars($shipping_info['ship_address1']);
            if (!empty($shipping_info['ship_city'])) $shipping_address_str .= ', ' . htmlspecialchars($shipping_info['ship_city']);
            if (!empty($shipping_info['ship_phone'])) $shipping_address_str .= ' (Phone: ' . htmlspecialchars($shipping_info['ship_phone']) . ')';
        } elseif (!empty($billing_info['bill_address1'])) {
            $shipping_address_str = htmlspecialchars($billing_info['bill_address1']);
            if (!empty($billing_info['bill_city'])) $shipping_address_str .= ', ' . htmlspecialchars($billing_info['bill_city']);
            if (!empty($billing_info['bill_phone'])) $shipping_address_str .= ' (Phone: ' . htmlspecialchars($billing_info['bill_phone']) . ')';
        }

        $email_body = '
        <div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff; color: #1e293b;">
            <div style="text-align: center; border-bottom: 2px solid ' . $badge_bg . '; padding-bottom: 15px; margin-bottom: 20px;">
                <h2 style="color: #0f172a; margin: 0 0 6px 0; font-size: 22px;">' . htmlspecialchars($site_title) . '</h2>
                <span style="background-color: ' . $badge_bg . '; color: #ffffff; font-size: 12px; font-weight: bold; padding: 4px 12px; border-radius: 20px; display: inline-block; letter-spacing: 0.5px;">' . $status_badge . '</span>
            </div>
            
            <p style="font-size: 15px; margin-bottom: 10px;">Assalam-o-Alaikum / Dear <strong>' . htmlspecialchars($user_name) . '</strong>,</p>
            <p style="font-size: 14px; line-height: 1.6; color: #334155;">' . $status_msg . '</p>
            
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; margin: 20px 0;">
                <table style="width: 100%; font-size: 13px; line-height: 1.8;">
                    <tr>
                        <td style="color: #64748b; width: 40%;"><strong>Order ID:</strong></td>
                        <td style="color: #0f172a; font-weight: bold;">#' . htmlspecialchars($order->transaction_number) . '</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;"><strong>Order Date:</strong></td>
                        <td style="color: #0f172a;">' . $order->created_at->format('M d, Y h:i A') . '</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;"><strong>Payment Method:</strong></td>
                        <td style="color: #0f172a;">' . htmlspecialchars($order->payment_method) . ' (' . htmlspecialchars($order->payment_status) . ')</td>
                    </tr>
                    ' . ($shipping_address_str ? '
                    <tr>
                        <td style="color: #64748b; vertical-align: top;"><strong>Delivery Address:</strong></td>
                        <td style="color: #0f172a;">' . $shipping_address_str . '</td>
                    </tr>' : '') . '
                </table>
            </div>

            <div style="margin: 20px 0;">
                <h4 style="margin: 0 0 10px 0; color: #0f172a; font-size: 15px; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px;">Items in Your Order:</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    ' . $items_rows . '
                    <tr>
                        <td colspan="2" style="padding: 12px 0 6px 0; text-align: right; font-weight: bold; color: #334155; font-size: 14px;">Total Amount:</td>
                        <td style="padding: 12px 0 6px 0; text-align: right; font-weight: bold; color: #0f172a; font-size: 16px;">' . htmlspecialchars($total_amount) . '</td>
                    </tr>
                </table>
            </div>

            <div style="text-align: center; margin: 30px 0 20px 0;">
                <a href="' . $track_url . '" style="background-color: #007bff; color: #ffffff; text-decoration: none; padding: 12px 26px; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 3px 8px rgba(0, 123, 255, 0.3);">🚚 Track Your Order Live</a>
            </div>
            
            <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 12px;">
                <p style="margin: 4px 0;">If you have any questions or need help, feel free to reply to this email.</p>
                <p style="margin: 4px 0;">&copy; ' . date('Y') . ' ' . htmlspecialchars($site_title) . '. All rights reserved.</p>
            </div>
        </div>';

        $emailData = [
            'to' => $user_email,
            'subject' => $subject,
            'body' => $email_body,
        ];

        try {
            if ($setting && $setting->is_queue_enabled == 1) {
                dispatch(new EmailSendJob($emailData, "custom"));
            } else {
                $emailHelper = new EmailHelper();
                $emailHelper->sendCustomMail($emailData);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Order status update email error: ' . $e->getMessage());
        }
    }

    /**
     * Send direct custom email to customer from order details
     */
    public function sendCustomerEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|max:200',
            'message' => 'required',
            'email' => 'required|email'
        ]);

        $order = Order::findOrFail($id);
        $setting = Setting::first();
        $site_title = $setting ? $setting->title : 'Namartzone';

        $email_subject = $request->subject;
        $email_body = '
        <div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 25px; border: 1px solid #e5e7eb; border-radius: 10px; background-color: #ffffff; color: #333333;">
            <div style="text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 15px; margin-bottom: 20px;">
                <h2 style="color: #007bff; margin: 0 0 5px 0;">' . htmlspecialchars($site_title) . '</h2>
                <p style="color: #6b7280; font-size: 14px; margin: 0;">Update regarding Order #' . htmlspecialchars($order->transaction_number) . '</p>
            </div>
            
            <div style="font-size: 15px; line-height: 1.7; color: #1e293b; margin-bottom: 20px; white-space: pre-line;">
                ' . nl2br(htmlspecialchars($request->message)) . '
            </div>
            
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 14px; border-radius: 8px; margin: 20px 0; font-size: 13px;">
                <p style="margin: 3px 0;"><strong>Order ID:</strong> #' . htmlspecialchars($order->transaction_number) . '</p>
                <p style="margin: 3px 0;"><strong>Order Status:</strong> ' . htmlspecialchars($order->order_status) . '</p>
                <p style="margin: 3px 0;"><strong>Payment Status:</strong> ' . htmlspecialchars($order->payment_status) . '</p>
            </div>
            
            <div style="text-align: center; margin: 25px 0 15px 0;">
                <a href="' . url('/order/track/submit?order_number=' . $order->transaction_number) . '" style="background-color: #007bff; color: #ffffff; text-decoration: none; padding: 10px 22px; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;">🚚 Track Your Order Live</a>
            </div>

            <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 12px;">
                <p style="margin: 4px 0;">If you have any questions, reply to this email or contact customer support.</p>
                <p style="margin: 4px 0;">&copy; ' . date('Y') . ' ' . htmlspecialchars($site_title) . '. All rights reserved.</p>
            </div>
        </div>';

        $emailData = [
            'to' => $request->email,
            'subject' => $email_subject,
            'body' => $email_body,
        ];

        try {
            if ($setting && $setting->is_queue_enabled == 1) {
                dispatch(new EmailSendJob($emailData, "custom"));
            } else {
                $emailHelper = new EmailHelper();
                $emailHelper->sendCustomMail($emailData);
            }
            return redirect()->back()->withSuccess(__('Email sent to customer successfully!'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Send customer email error: ' . $e->getMessage());
            return redirect()->back()->withErrors([__('Failed to send email: ') . $e->getMessage()]);
        }
    }

}
