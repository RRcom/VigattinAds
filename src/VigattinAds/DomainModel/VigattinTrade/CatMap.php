<?php
namespace VigattinAds\DomainModel\VigattinTrade;

class CatMap {

    static $categoryMap = array(
        "vehicles" => array(
            "id" => 1,
            "cat" => array(
                "all"                           => array("id" => "all", "cat" => array()),
                "car accessories & parts"       => array(
                    "id" => 5,
                    "cat" => array(
                        "audio/video devices"           => array("id" => 44, "cat" => array()),
                        "body & main parts"             => array("id" => 113, "cat" => array()),
                        "braking systems"               => array("id" => 45, "cat" => array()),
                        "car seats & interiors"         => array("id" => 92, "cat" => array()),
                        "car-care"                      => array("id" => 46, "cat" => array()),
                        "decorative"                    => array("id" => 47, "cat" => array()),
                        "electrical & electronics"      => array("id" => 48, "cat" => array()),
                        "engine components"             => array("id" => 99, "cat" => array()),
                        "filtration systems"            => array("id" => 49, "cat" => array()),
                        "for security & emergency"      => array("id" => 53, "cat" => array()),
                        "lights and hid"                => array("id" => 50, "cat" => array()),
                        "mags and tires"                => array("id" => 51, "cat" => array()),
                        "mufflers"                      => array("id" => 102, "cat" => array()),
                        "oils and lubricants"           => array("id" => 52, "cat" => array()),
                        "others"                        => array("id" => 55, "cat" => array()),
                        "suspension & steering systems" => array("id" => 54, "cat" => array()),
                    ),
                ),
                "car services"                  => array(
                    "id" => 6,
                    "cat" => array(
                        "airconditioning"               => array("id" => 27, "cat" => array()),
                        "auto detaling"                 => array("id" => 29, "cat" => array()),
                        "car insurance"                 => array("id" => 30, "cat" => array()),
                        "car loans"                     => array("id" => 28, "cat" => array()),
                        "others"                        => array("id" => 96, "cat" => array()),
                        "repair and under chassis"      => array("id" => 26, "cat" => array()),
                        "trucking"                      => array("id" => 31, "cat" => array()),
                    )
                ),
                "cars for rent"                 => array("id" => 68, "cat" => array()),
                "cars, sedans & minivans"       => array("id" => 1, "cat" => array()),
                "coupés & convertibles"         => array("id" => 39, "cat" => array()),
                "heavy equipment"               => array("id" => 143, "cat" => array()),
                "heavy equipment for rent"      => array("id" => 74, "cat" => array()),
                "hybrids & evs"                 => array("id" => 40, "cat" => array()),
                "luxury & sports cars"          => array("id" => 41, "cat" => array()),
                "motorcycle parts & acc"        => array(
                    "id" => 8,
                    "cat" => array(
                        "audio"                         => array("id" => 98, "cat" => array()),
                        "braking systems"               => array("id" => 97, "cat" => array()),
                        "fairings"                      => array("id" => 59, "cat" => array()),
                        "helmets and safety gears"      => array("id" => 56, "cat" => array()),
                        "mags and tires"                => array("id" => 57, "cat" => array()),
                        "mufflers"                      => array("id" => 58, "cat" => array()),
                        "others"                        => array("id" => 95, "cat" => array()),
                        "suspension system"             => array("id" => 103, "cat" => array()),
                )),
                "motorcycles & scooters"        => array("id" => 7, "cat" => array()),
                "suvs, crossovers & pickups"    => array("id" => 2, "cat" => array()),
                "trucks and buses for rent"     => array("id" => 69, "cat" => array()),
                "trucks, trailers & buses"      => array("id" => 4, "cat" => array()),
                "vans & rvs"                    => array("id" => 3, "cat" => array()),
                "other vehicles"                => array("id" => 104, "cat" => array()),
            )
        ),
        "real estate" => array(
            "id" => 2,
            "cat" => array(
                "all"                           => array("id" => "all", "cat" => array()),
                "apartments & condos"           => array("id" => 9, "cat" => array()),
                "beaches & resorts"             => array("id" => 11, "cat" => array()),
                "commercial & industrial"       => array("id" => 12, "cat" => array()),
                "house & lots, townhouses"      => array("id" => 10, "cat" => array()),
                "lands & farms"                 => array("id" => 13, "cat" => array()),
                "memorial lot areas"            => array("id" => 14, "cat" => array()),
                "real estate services"          => array("id" => 15, "cat" => array(
                    "appraisals & brokerage"        => array("id" => 36, "cat" => array()),
                    "architecture"                  => array("id" => 33, "cat" => array()),
                    "contracting"                   => array("id" => 32, "cat" => array()),
                    "interior designs"              => array("id" => 34, "cat" => array()),
                    "landscape architecture"        => array("id" => 38, "cat" => array()),
                    "others"                        => array("id" => 109, "cat" => array())
                )),
                "rooms & bedspace for rent"     => array("id" => 75, "cat" => array())
            )
        ),
        "general category"=> array(
            "id" => 4,
            "cat" => array(
                "all"                               => array("id" => "all", "cat" => array()),
                "animals & pets"                    => array("id" => 64, "cat" => array(
                    "accessories"                   => array("id" => 125, "cat" => array()),
                    "birds"                         => array("id" => 126 , "cat" => array()),
                    "cats & dogs"                   => array("id" => 127 , "cat" => array()),
                    "exotic"                        => array("id" => 128 , "cat" => array()),
                    "fish"                          => array("id" => 129 , "cat" => array()),
                    "livestock"                     => array("id" => 130 , "cat" => array()),
                    "others"                        => array("id" => 131 , "cat" => array())
                )),
                "appliances"                        => array("id" => 24, "cat" => array()),
                "arts & crafts"                     => array("id" => 119, "cat" => array()),
                "baby stuffs"                       => array("id" => 115, "cat" => array()),
                "books & other publications"        => array("id" => 175, "cat" => array()),
                "chemicals"                         => array("id" => 100, "cat" => array()),
                "computers & related items"         => array("id" => 116, "cat" => array(
                    "laptops & notebooks"           => array("id" => 134, "cat" => array()),
                    "monitors"                      => array("id" => 196, "cat" => array()),
                    "others"                        => array("id" => 180, "cat" => array()),
                    "parts & accessories"           => array("id" => 135, "cat" => array()),
                    "printers and scanners"         => array("id" => 195, "cat" => array()),
                    "software"                      => array("id" => 136, "cat" => array()),
                    "storage"                       => array("id" => 137, "cat" => array())
                )),
                "electronics"                       => array("id" => 21, "cat" => array(
                    "audio & video"                 => array("id" => 138, "cat" => array()),
                    "cameras"                       => array("id" => 139, "cat" => array()),
                    "e-books"                       => array("id" => 142, "cat" => array()),
                    "gadgets"                       => array("id" => 140, "cat" => array()),
                    "others"                        => array("id" => 176, "cat" => array())
                )),
                "farming & gardening"               => array("id" => 120, "cat" => array()),
                "food & related items"              => array("id" => 23, "cat" => array(
                    "cooking equipment"             => array("id" => 143, "cat" => array()),
                    "drinks & beverages"            => array("id" => 144, "cat" => array()),
                    "fruits & vegetables"           => array("id" => 147, "cat" => array()),
                    "others"                        => array("id" => 150, "cat" => array()),
                    "seafoods & meats"              => array("id" => 145, "cat" => array()),
                    "seasoning & condiments"        => array("id" => 148, "cat" => array()),
                    "snacks, desserts & delicacies" => array("id" => 149, "cat" => array())

                )),
                "footwear, clothing & acc."         => array("id" => 25, "cat" => array(
                    "bags & wallets"                => array("id" => 151, "cat" => array()),
                    "casual & formal wear"          => array("id" => 152, "cat" => array()),
                    "costumes"                      => array("id" => 153, "cat" => array()),
                    "gowns & dresses"               => array("id" => 154, "cat" => array()),
                    "head & hair accessories"       => array("id" => 155, "cat" => array()),
                    "jewelries & watches"           => array("id" => 156, "cat" => array()),
                    "others"                        => array("id" => 177, "cat" => array()),
                    "pants & jeans"                 => array("id" => 157, "cat" => array()),
                    "shirts & blouses"              => array("id" => 159, "cat" => array()),
                    "shoes, slippers & sandals"     => array("id" => 160, "cat" => array()),
                    "shorts & skirts"               => array("id" => 161, "cat" => array()),
                    "socks & stockings"             => array("id" => 158, "cat" => array()),
                    "sportswear"                    => array("id" => 174, "cat" => array()),
                    "undergarments"                 => array("id" => 162, "cat" => array())
                )),
                "for security & emergency"          => array("id" => 114, "cat" => array()),
                "health, wellness & beauty"         => array("id" => 63, "cat" => array(
                    "cosmetic products"             => array("id" => 163, "cat" => array()),
                    "fitness equipment"             => array("id" => 164, "cat" => array()),
                    "others"                        => array("id" => 178, "cat" => array()),
                    "perfumes"                      => array("id" => 197, "cat" => array()),
                    "salon items & equipment"       => array("id" => 165, "cat" => array()),
                    "vitamins & food supplement"    => array("id" => 166, "cat" => array())
                )),
                "household & industrial items"      => array("id" => 66, "cat" => array(
                    "cleaning materials"            => array("id" => 194, "cat" => array()),
                    "furnitures & fixtures"         => array("id" => 170, "cat" => array()),
                    "kitchenwares"                  => array("id" => 171, "cat" => array()),
                    "machineries & equipment"       => array("id" => 172, "cat" => array()),
                    "others"                        => array("id" => 179, "cat" => array()),
                    "tools, generators & accessories"   => array("id" => 173, "cat" => array())
                )),
                "medical supplies & equipment"      => array("id" => 106, "cat" => array()),
                "metals, woods glasses & stones"    => array("id" => 117, "cat" => array()),
                "musical instruments"               => array("id" => 101, "cat" => array()),
                "networking & telecommunications"   => array("id" => 183, "cat" => array()),
                "office & school supplies"          => array("id" => 107, "cat" => array()),
                "party needs"                       => array("id" => 182, "cat" => array()),
                "phones & accessories"              => array("id" => 141, "cat" => array()),
                "playthings and collectibles"       => array("id" => 108, "cat" => array()),
                "services"                          => array("id" => 67, "cat" => array(
                    "advertising and publishing"         => array("id" => 76, "cat" => array()),
                    "animal services"                    => array("id" => 167, "cat" => array()),
                    "arts & entertainment"               => array("id" => 110, "cat" => array()),
                    "brokerage and investment"           => array("id" => 122, "cat" => array()),
                    "clothing & textile services"        => array("id" => 86, "cat" => array()),
                    "consulting"                         => array("id" => 121, "cat" => array()),
                    "courier & logistics"                => array("id" => 124, "cat" => array()),
                    "employment & human resources"       => array("id" => 123, "cat" => array()),
                    "engineering services"               => array("id" => 79, "cat" => array()),
                    "events & planning"                  => array("id" => 80, "cat" => array()),
                    "food services"                      => array("id" => 168, "cat" => array()),
                    "hair care"                          => array("id" => 198, "cat" => array()),
                    "health & medical services"          => array("id" => 81, "cat" => array()),
                    "information technology"             => array("id" => 82, "cat" => array()),
                    "installation services"              => array("id" => 112, "cat" => array()),
                    "landscaping services"               => array("id" => 111, "cat" => array()),
                    "leisure & recreational activities"  => array("id" => 169, "cat" => array()),
                    "loans & insurance"                  => array("id" => 83, "cat" => array()),
                    "manufacturing & franchising"        => array("id" => 77, "cat" => array()),
                    "marketing and sales"                => array("id" => 192, "cat" => array()),
                    "massage services"                   => array("id" => 84, "cat" => array()),
                    "memorial services"                  => array("id" => 85, "cat" => array()),
                    "online business"                    => array("id" => 181, "cat" => array()),
                    "other services"                     => array("id" => 105, "cat" => array()),
                    "outsourcing & contracting"          => array("id" => 94, "cat" => array()),
                    "rental services"                    => array("id" => 87, "cat" => array()),
                    "repairs and maintenance"            => array("id" => 88, "cat" => array()),
                    "souvenirs & giveaways"              => array("id" => 78, "cat" => array()),
                    "tutorials, classes & workshops"     => array("id" => 89, "cat" => array())
                )),
                "sports accessories & equipment"    => array("id" => 93, "cat" => array()),
                "travels and hotels"                => array("id" => 65, "cat" => array()),
                "other items"                       => array("id" => 91, "cat" => array())
            )
        ),
    );

    public function getCategoryMap()
    {
        return $this->categoryMap;
    }

    public function convertCatToKeyword($category)
    {
        $finalKeyword = '';
        $keywordArray = explode('|', $category);
        if(count($keywordArray)) {
            foreach($keywordArray as $key => $singleKey) {
                $finalKeyword .= $this->createAbsoluteCat($keywordArray, $key);
            }
        }
        return $finalKeyword;
    }

    static public function createAbsoluteCat($keywordArray, $position, $rootKey = '')
    {
        $absoluteCat = '';
        $catCount = count($keywordArray);
        if($catCount && ($position < $catCount)) {
            for($i = 0; $i <= $position; $i++) {
                $absoluteCat .= $keywordArray[$i].' ';
            }
            $absoluteCat = trim($absoluteCat);
            if($absoluteCat) {
                if($rootKey) $absoluteCat = '('.$rootKey.' '.$absoluteCat.')';
                else $absoluteCat = '('.$absoluteCat.')';
            }
        }
        return $absoluteCat;
    }

    static public function generatePreviewUrl($keywordArray, $position)
    {
        $mainUrl = '//www.vigattintrade.com/results/browse/';
        $currentMap = '';
        $catCount = count($keywordArray);
        if($catCount && ($position < $catCount)) {
            foreach($keywordArray as $key => $value) {
                if($key <= $position) {
                    $value = strtolower($value);
                    switch($key) {
                        case 1:
                            $currentMap = self::$categoryMap[$value]['cat'];
                            $mainUrl .= self::$categoryMap[$value]['id'].'/';
                            break;
                        case 2:
                            $mainUrl .= $currentMap[$value]['id'].'/';
                            $currentMap = $currentMap[$value]['cat'];
                            break;
                        case 3:
                            $mainUrl += '?service='+$currentMap[$value]['id'];
                            break;
                    }
                }
            }
        }
        return $mainUrl;
    }
}