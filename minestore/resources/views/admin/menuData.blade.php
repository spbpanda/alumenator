<?php
$menuData = (object) [
    "menu" => [
        [
            "name" => __("Dashboard"),
            "icon" => "menu-icon tf-icons bx bxs-store",
            "slug" => "index",
            "rule" => "dashboard",
        ],
        [
            "menuHeader" => __("Webstore")
        ],
        [
            "name" => __("Packages"),
            "icon" => "menu-icon tf-icons bx bxs-package",
            "slug" => "items.index",
            "rule" => "packages",
        ],
        [
            "name" => __("Settings"),
            "icon" => "menu-icon tf-icons bx bx-cog",
            "slug" => "settings",
            "rule" => "settings",
            "submenu" => [
                [
                    "name" => __("General Settings"),
                    "slug" => "settings.index",
                ],
                [
                    "name" => __("Email Settings"),
                    "slug" => "settings.email",
                ],
                [
                    "name" => __("Authorization Settings"),
                    "slug" => "settings.authType",
                ],
                [
                    "name" => __("Payment Gateways"),
                    "slug" => "settings.merchant",
                ],
                [
                    "name" => __("Currency Management"),
                    "slug" => "settings.currencyManagement",
                ],
                [
                    "name" => __("Minecraft Servers"),
                    "slug" => "settings.servers.index",
                ],
            ],
        ],
        [
            "name" => __("CMS Settings"),
            "icon" => "menu-icon tf-icons bx bx-book-content",
            "slug" => "dashboard",
            "rule" => "cms",
            "submenu" => [
                [
                    "name" => __("Homepage Content"),
                    "slug" => "settings.homepage",
                ],
                [
                    "name" => __("Social Media"),
                    "slug" => "settings.social",
                ],
                [
                    "name" => __("Links"),
                    "slug" => "settings.links",
                ],
                [
                    "name" => __("Custom Pages"),
                    "slug" => "pages.index",
                ],
                [
                    "name" => __("Staff Page"),
                    "slug" => "pages.staff",
                ],
                [
                    "name" => __("Profile Page"),
                    "slug" => "pages.profile",
                ],
                [
                    "name" => __("Resetting Tool"),
                    "slug" => "settings.resetting",
                ],
            ],
        ],
        [
            "name" => __("Variables"),
            "icon" => "menu-icon tf-icons bx bx-code-curly",
            "slug" => "vars.index",
            "rule" => "variables",
        ],
        [
            "name" => __("Announcement"),
            "icon" => "menu-icon tf-icons bx bx-tv",
            "slug" => "advert.index",
            "rule" => "announcement",
        ],
        [
            "name" => __("Themes"),
            "icon" => "menu-icon tf-icons bx bx-palette",
            "slug" => "themes.index",
            "rule" => "themes",
        ],
        [
            "name" => __("Discord Settings"),
            "icon" => "menu-icon tf-icons bx bxl-discord-alt",
            "slug" => "discord.index",
            "rule" => "settings",
        ],
        [
            "menuHeader" => __("Sales")
        ],
        [
            "name" => __("Engagement"),
            "icon" => "menu-icon tf-icons bx bxs-offer",
            "slug" => "coupons.index",
            "rule" => "discounts",
            "submenu" => [
                [
                    "name" => __("Coupons"),
                    "slug" => "coupons.index",
                ],
                [
                    "name" => __("Gift Cards"),
                    "slug" => "gifts.index",
                ],
                [
                    "name" => __("Sales"),
                    "slug" => "sales.index",
                ],
            ],
        ],
        [
            "name" => __("Featured Deals"),
            "icon" => "menu-icon tf-icons bx bx-crown",
            "slug" => "settings.featured",
            "rule" => "packages",
        ],
        [
            "name" => __("Promoted Packages"),
            "icon" => "menu-icon tf-icons bx bx-basket",
            "slug" => "promoted.index",
            "rule" => "promoted_packages",
        ],
        [
            "name" => __("Donation Goals"),
            "icon" => "menu-icon tf-icons bx bx-candles",
            "slug" => "donation_goals.index",
            "rule" => "donation_goals",
        ],
        [
            "name" => __("Taxes"),
            "icon" => "menu-icon tf-icons bx bxs-badge-dollar",
            "slug" => "taxes.index",
            "rule" => "taxes",
        ],
        [
            "name" => __("Player Referrals"),
            "icon" => "menu-icon tf-icons bx bx-group",
            "slug" => "refs.index",
            "rule" => "referers",
        ],
        [
            "name" => __("Transactions"),
            "icon" => "menu-icon tf-icons bx bx-money-withdraw",
            "slug" => "payments.index",
            "rule" => "payments",
        ],
        [
            "name" => __("Subscriptions"),
            "icon" => "menu-icon tf-icons bx bx-card",
            "slug" => "subscriptions.index",
            "rule" => "subs",
        ],
        [
            "name" => __("Statistics"),
            "icon" => "menu-icon tf-icons bx bx-pie-chart",
            "slug" => "statistics.index",
            "rule" => "statistics",
        ],
        [
            "menuHeader" => __("Fraud Preventation")
        ],
        [
            "name" => __("Fraud"),
            "icon" => "menu-icon tf-icons bx bxs-mask",
            "slug" => "dashboard",
            "rule" => "fraud",
            "submenu" => [
                [
                    "name" => __("Chargeback Prevention"),
                    "slug" => "chargeback.settings",
                ],
                [
                    "name" => __("Chargeback List"),
                    "slug" => "chargeback.index",
                ],
                [
                    "name" => __("Spending Limit"),
                    "slug" => "chargeback.spendinglimit",
                ],
                [
                    "name" => __("IP Checks Settings"),
                    "slug" => "ipchecks.index",
                ],
            ],
        ],
        [
            "name" => __("Customers"),
            "icon" => "menu-icon tf-icons bx bxs-store-alt",
            "slug" => "dashboard",
            "rule" => "bans",
            "submenu" => [
                [
                    "name" => __("Banlist"),
                    "slug" => "bans.index",
                ],
                [
                    "name" => __("Whitelist"),
                    "slug" => "whitelist.index",
                ],
                [
                    "name" => __("User Lookup"),
                    "slug" => "lookup.index",
                ],
            ],
        ],
        [
            "menuHeader" => __("Extras")
        ],
        [
            "name" => __("Admin Users"),
            "icon" => "menu-icon tf-icons bx bxs-hand",
            "slug" => "users.index",
            "rule" => "teams",
        ],
        [
            "name" => __("Security logs"),
            "icon" => "menu-icon tf-icons bx bx-shield-quarter",
            "slug" => "securityLogs.index",
            "rule" => "teams",
        ],
        [
            "name" => __("Global Commands"),
            "icon" => "menu-icon tf-icons bx bx-shape-polygon",
            "slug" => "globalCommands.index",
            "rule" => "global_commands",
        ],
        [
            "name" => __("API Settings"),
            "icon" => "menu-icon tf-icons bx bxs-lock-alt",
            "slug" => "apiAccessSettings.index",
            "rule" => "api",
        ],
    ],
];
