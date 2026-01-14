<?php

namespace App\Utils;

class MaterialIcon
{
    private static $iconList = [
        'restaurant',
        'lunch_dining',
        'fastfood',
        'bakery_dining',
        'cake',
        'local_cafe',
        'coffee',
        'icecream',
        'local_bar',
        'liquor',
        'water_drop',
        'egg',
        'breakfast_dining',
        'kitchen',
        'nutrition',
        'home',
        'cleaning_services',
        'lightbulb',
        'living',
        'weekend',
        'bed',
        'chair',
        'hardware',
        'spa',
        'clean_hands',
        'sanitizer',
        'face',
        'medication',
        'healing',
        'medical_services',
        'health_and_safety',
        'fitness_center',
        'child_care',
        'child_friendly',
        'baby_changing_station',
        'stroller',
        'toys',
        'school',
        'menu_book',
        'edit',
        'design_services',
        'book',
        'article',
        'inventory',
        'smartphone',
        'devices',
        'laptop',
        'headphones',
        'memory',
        'print',
        'wifi',
        'checkroom',
        'styler',
        'dry_cleaning',
        'watch',
        'shopping_bag',
        'store',
        'storefront',
        'local_grocery_store',
        'shopping_cart',
        'local_offer',
        'card_giftcard',
        'star',
        'new_releases',
        'loyalty',
        'category',
        'redeem',
        'sell',
    ];

    public static function getAllIcon()
    {
        return self::$iconList;
    }

    public static function getIcon($icon)
    {
        // Handle if index is passed or icon name is passed directly (fallback)
        if (is_numeric($icon) && isset(self::$iconList[$icon])) {
            $iconName = self::$iconList[$icon];
        } else {
            // Fallback if the saved data is already the icon name or invalid index
            $iconName = $icon;
        }

        return '<span class="material-symbols-outlined">'.$iconName.'</span>';
    }
}
