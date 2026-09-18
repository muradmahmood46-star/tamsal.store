<?php

namespace App\Helpers;

use App\Models\Page;

class Helper
{

 
    public static function renderStarRating($rating, $maxRating = 5)
    {
        if ($rating instanceof \App\Models\Item) {
            $rating = $rating->rating;
        } elseif (is_numeric($rating)) {
            $rating = (float) $rating;
        } else {
            $rating = 0.0;
        }

        $rating = (float) $rating;
        $rating = $rating <= $maxRating ? $rating : $maxRating;
        $rating = max(0, $rating);

        $fullStar = "<i class='fas fa-star filled text-warning' style='color: #f59e0b !important;'></i>";
        $halfStar = "<i class='fas fa-star-half-alt filled text-warning' style='color: #f59e0b !important;'></i>";
        $emptyStar = "<i class='far fa-star text-muted' style='color: #cbd5e1 !important;'></i>";

        $fullStarCount = (int) floor($rating);
        $decimal = $rating - $fullStarCount;
        $halfStarCount = 0;
        if ($decimal >= 0.25 && $decimal <= 0.75) {
            $halfStarCount = 1;
        } elseif ($decimal > 0.75) {
            $fullStarCount++;
        }
        $emptyStarCount = max(0, $maxRating - $fullStarCount - $halfStarCount);

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);

        return $html;
    }
    
    public static function getHref($link){
          
     
        $href = "#";

        if ($link["type"] == 'home') {
            $href = route('front.index');
        } 
        else if ($link["type"] == 'shop') {
            $href = route('front.catalog');
        } 
        else if ($link["type"] == 'campaign') {
            $href = route('front.campaign');
        } 
        else if ($link["type"] == 'brand') {
            $href = route('front.brand');
        } 
        else if ($link["type"] == 'blog') {
            $href = route('front.blog');
        }
        else if ($link["type"] == 'faq') {
            $href = route('front.faq');
        } 
        else if ($link["type"] == 'contact') {
            $href = route('front.contact');
        } 
        else {
            $pageid = (int)$link["type"];
            $page = Page::find($pageid);
            if (!empty($page)) {
                $href = route('front.page', [$page->slug]);
            } else {
                $href = '#';
            }
        }

        return $href;
        
    }

    public static function createMenu($arr){
        echo '<ul style="z-index: 0;" class="submenu">';
        foreach ($arr["children"] as $el) {

            // determine the href
            $href = Helper::getHref($el);

            echo '<li>';
            echo '<a  href="'.$href.'" target="'.$el["target"].'">'.$el["text"].'</a>';
            if (array_key_exists("children", $el)) {
                Helper::createMenu($el);
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    

}


