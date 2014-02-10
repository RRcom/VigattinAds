<?php
namespace VigattinAds\DomainModel\VigattinTrade;

class Category
{
    protected $categoryName = array(
        'vehicles' => array(
            'all'                           => 'tv',
            'car accessories & parts'       => 'tva',
            'car services'                  => 'tvb',
            'cars for rent'                 => 'tvc',
            'cars, sedans & minivans'       => 'tvd',
            'coupés & convertibles'         => 'tve',
            'heavy equipment'               => 'tvf',
            'heavy equipment for rent'      => 'tvg',
            'hybrids & evs'                 => 'tvh',
            'luxury & sports cars'          => 'tvi',
            'motorcycle parts & acc'        => 'tvj',
            'motorcycles & scooters'        => 'tvk',
            'suvs, crossovers & pickups'    => 'tvl',
            'trucks and buses for rent'     => 'tvm',
            'trucks, trailers & buses'      => 'tvn',
            'vans & rvs'                    => 'tvo',
            'other vehicles'                => 'tvp',
        ),
        'real estate' => array(
            'all'                           => 'tr',
            'apartments & condos'           => 'tra',
            'beaches & resorts'             => 'trb',
            'commercial & industrial'       => 'trc',
            'house & lots, townhouses'      => 'trd',
            'lands & farms'                 => 'tre',
            'memorial lot areas'            => 'trf',
            'real estate services'          => 'trg',
            'rooms & bedspace for rent'     => 'trh',
        ),
        'general category' => array(
            'all'                               => 'tg',
            'animals & pets'                    => 'tga',
            'appliances'                        => 'tgb',
            'arts & crafts'                     => 'tgc',
            'baby stuffs'                       => 'tgd',
            'books & other publications'        => 'tge',
            'chemicals'                         => 'tgf',
            'computers & related items'         => 'tgg',
            'electronics'                       => 'tgh',
            'farming & gardening'               => 'tgi',
            'food & related items'              => 'tgj',
            'footwear, clothing & acc.'         => 'tgk',
            'for security & emergency'          => 'tgl',
            'health, wellness & beauty'         => 'tgm',
            'household & industrial items'      => 'tgn',
            'medical supplies & equipment'      => 'tgo',
            'metals, woods glasses & stones'    => 'tgp',
            'musical instruments'               => 'tgq',
            'networking & telecommunications'   => 'tgr',
            'office & school supplies'          => 'tgs',
            'party needs'                       => 'tgt',
            'phones & accessories'              => 'tgu',
            'playthings and collectibles'       => 'tgv',
            'services'                          => 'tgw',
            'sports accessories & equipment'    => 'tgx',
            'travels and hotels'                => 'tgy',
            'other items'                       => 'tgz',
        ),
    );

    public function getCategoryList()
    {
        return $this->categoryName;
    }

    public function getAlias($categoryName, $subName)
    {
        $cat = $this->categoryName[strtolower($categoryName)];
        $alias = isset($cat[strtolower($subName)]) ? $cat[strtolower($subName)] : '';
        return $alias;
    }
}