<?php declare(strict_types=1);
/**
 * To plugin root file.
 *
 * Copyright (C) BrandCrock GmbH. All rights reserved
 *
 * If you have found this script useful a small
 * recommendation as well as a comment on our
 * home page(https://brandcrock.com/)
 * would be greatly appreciated.
 *
 * @author BrandCrock GmbH
 * @package BrandCrockSoftTheme
 */
namespace Bc\BrandCrockSoftTheme;

use Shopware\Core\Framework\Plugin;
use Shopware\Storefront\Framework\ThemeInterface;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Core\Framework\Uuid\Uuid;
use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Context;

class BrandCrockSoftTheme extends Plugin implements ThemeInterface
{    
	private $connection;
    private $fileSaver;
    private $context;   
    // Defalut media folders
    private $bc_default_folder_name = [
        ['id'=> 'b4a3283101ee4bf9bec52bef6150dec6'],
        ['id'=> '02e88775a78145c0bbadb6f3994044be'],
        ['id'=> '7bf115d1f5ec44558ef2e07bf8251d77'],
        ['id'=> 'c9d779866a2c4e2d942b90d45b81212e'],
        ['id'=> '802c2c954d3b47d0a056e1cb50057687'],
        ['id'=> 'a2d7329d24454b189c513a1684088366'],
        ['id'=> 'e612b1e4514b48a19a58f13b5f7f8f05'],       
        ['id'=> 'b6a443e632f346bb82296fa3c52c67cd'],
        ['id'=> '3cb9c5d8864c4bc384335ee8dcb1e4ee'],
    ];

    // Demo media files name
    private $bc_media_files_ary = ['banner_slider_1', 'banner_slider_2', 'banner_slider_3', 'girl_shoe', 'boy_shoe', 'girls_clothes', 'boys_clothes', 'girl_spends', 'room_wall'];
     // Theme media files name
    private $bc_theme_media_files_ary = ['bc_st_logo', 'bc_st_favicon'];
    // BC media path
    private $mpath = __DIR__ . '/Resources/media/';

    public function getThemeConfigPath(): string
    {        
        return 'theme.json';
    }
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
    
    public function install(InstallContext $installContext): void
    {
        $this->connection = $this->container->get(Connection::class);        
        $this->fileSaver = $this->container->get(FileSaver::class);
        $this->context = Context::createDefaultContext();
        $this->createBcCmsPage($this->connection);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        $this->connection = $this->container->get(Connection::class);
        $this->removeBcCmsPage($this->connection);
        if ($uninstallContext->keepUserData()) {
            parent::uninstall($uninstallContext);
            return;
        }
        parent::uninstall($uninstallContext);
    }

    /**
     * Create Cms page entires and configuration
     * @param $connection object
    */
    private function createBcCmsPage(Connection $connection): void
    {
        $languageEn = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageDe = $this->getLanguageDeId($this->connection);
        $versionId = Uuid::fromHexToBytes(Defaults::LIVE_VERSION);
        $cmsFolder = $this->getDefaultFolderIdForEntity('cms_page');
        // Create dynamic media folder
        $bcMediaFiles = [
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomHex(),
                'media_folder_id' => Uuid::fromHexToBytes($cmsFolder),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
        ];
                
        $result = 0;
        // Insert theme media files into the media table
        foreach ($bcMediaFiles as $bkey => $bcMediaFile) {
            rename($this->mpath.$this->bc_default_folder_name[$bkey]['id'], $this->mpath.$bcMediaFiles[$bkey]['id']);
            $arrOldNewpath[$this->mpath.$bcMediaFiles[$bkey]['id']] = $this->mpath.$this->bc_default_folder_name[$bkey]['id'];
            $bcMediaFile['id'] = Uuid::fromHexToBytes($bcMediaFile['id']);
            $result = $this->connection->insert('media', $bcMediaFile);
        }       
        // To map media folder images to the inserted media entires
        if ($result) {
            foreach (glob(__DIR__ . '/Resources/media/*/*.jpg') as $file) {
                $this->fileSaver->persistFileToMedia(
                    new MediaFile(
                        $file,
                        mime_content_type($file),
                        pathinfo($file, PATHINFO_EXTENSION),
                        filesize($file)
                    ),
                    pathinfo($file, PATHINFO_FILENAME),
                    basename(dirname($file)),
                    $this->context
                );
            }
        }
        
        // Create cms page
        $page = [
            'id' => Uuid::randomBytes(),
            'type' => 'landingpage',
            'locked' => 0,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];
        $pageEng = [
            'cms_page_id' => $page['id'],
            'language_id' => $languageEn,
            'name' => 'SoftTheme Home Page',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];
        $pageDeu = [
            'cms_page_id' => $page['id'],
            'language_id' => $languageDe,
            'name' => 'SoftTheme Home Page',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];

        $this->connection->insert('cms_page', $page); // Insert cms page
        $this->connection->insert('cms_page_translation', $pageEng); // Insert the cms page language
        if ($languageDe) {
            $this->connection->insert('cms_page_translation', $pageDeu); // Insert the cms page language
        }
        
        // cms page section.
        $bcSections = [
            [
                'id' => Uuid::randomBytes(),
                'cms_page_id' => $page['id'],
                'position' => 0,
                'type' => 'default',
                'name' => 'bc_st_top_banner',
                'sizing_mode' => 'full_width',
                'css_class' => 'bc-st-top-banner',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomBytes(),
                'cms_page_id' => $page['id'],
                'position' => 1,
                'type' => 'default',
                'name' => 'bc_st_product_slider_one',
                'sizing_mode' => 'boxed',
                'css_class' => 'bc-st-product-slider-one',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomBytes(),
                'cms_page_id' => $page['id'],
                'position' => 2,
                'type' => 'default',
                'name' => 'bc_st_desc_div_one',
                'sizing_mode' => 'boxed',
                'background_media_mode' => 'cover',
                'css_class' => 'bc-st-desc-div-one',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomBytes(),
                'cms_page_id' => $page['id'],
                'position' => 3,
                'type' => 'default',
                'name' => 'bc_st_category_list',
                'sizing_mode' => 'boxed',
                'background_media_mode' => 'cover',
                'css_class' => 'bc-st-category-list',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomBytes(),
                'cms_page_id' => $page['id'],
                'position' => 4,
                'type' => 'default',
                'name' => 'bc_st_desc_div_two',
                'sizing_mode' => 'boxed',
                'background_media_mode' => 'cover',
                'css_class' => 'bc-st-desc-div-two',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],            
            [
                'id' => Uuid::randomBytes(),
                'cms_page_id' => $page['id'],
                'position' => 5,
                'type' => 'default',
                'name' => 'bc_st_promotion_image',
                'sizing_mode' => 'full_width',
                'css_class' => 'bc_st_promotion_image',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::randomBytes(),
                'cms_page_id' => $page['id'],
                'position' => 6,
                'type' => 'default',
                'name' => 'bc_st_product_slider_two',
                'sizing_mode' => 'boxed',
                'css_class' => 'bc_st_product_slider_two',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]
        ];
        
        // Insert cms section.
        foreach ($bcSections as $bcSection) {
            $this->connection->insert('cms_section', $bcSection);
        }
        
        // cms blocks
        $blocks = [
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[0]['id'],
                'locked' => 0,
                'position' => 0,
                'type' => 'image-cover',
                'name' => 'bc_st_top_banner_blk',
                'background_media_mode' => 'cover',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[1]['id'],
                'locked' => 0,
                'position' => 1,
                'section_position' => 'main',
                'type' => 'product-slider',
                'name' => 'bc_st_new_arrival_products_slider',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[2]['id'],
                'locked' => 0,
                'position' => 2,
                'section_position' => 'main',
                'type' => 'text-hero',
                'name' => 'bc_st_product_description',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' =>$bcSections[3]['id'],
                'locked' => 0,
                'position' => 3,
                'section_position' => 'main',
                'type' => 'image-four-column',
                'name' => 'bc_st_product_category',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[4]['id'],
                'locked' => 0,
                'position' => 2,
                'section_position' => 'main',
                'type' => 'text-hero',
                'name' => 'bc_st_product_description',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[5]['id'],
                'locked' => 0,
                'position' => 0,
                'section_position' => 'main',
                'type' => 'image-two-column',
                'name' => 'bc_st_offer_banner',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[5]['id'],
                'locked' => 0,
                'position' => 1,
                'section_position' => 'main',
                'type' => 'text-hero',
                'name' => 'bc_st_offer_banner_promo',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[5]['id'],
                'locked' => 0,
                'position' => 2,
                'section_position' => 'main',
                'type' => 'text-hero',
                'name' => 'bc_st_offer_banner_promo_2',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ],
            [
                'id' => Uuid::randomBytes(),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'cms_section_id' => $bcSections[6]['id'],
                'locked' => 0,
                'position' => 0,
                'section_position' => 'main',
                'type' => 'product-slider',
                'name' => 'bc_st_bestseller_slider',
                'background_media_mode' => 'cover',
                'margin_top' => '20px',
                'margin_left' => '20px',
                'margin_bottom' => '20px',
                'margin_right' => '20px',
            ]
        ];

        foreach ($blocks as $block) {
            $this->connection->insert('cms_block', $block);
        }
        $arrStoreBrandImgUrl = $this->getStoreBrandsId();
        // cms slots
        $slots = [
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[0]['id'], 'type' => 'image', 'slot' => 'image', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[1]['id'], 'type' => 'product-slider', 'slot' => 'productSlider', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[2]['id'], 'type' => 'text', 'slot' => 'content', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[3]['id'], 'type' => 'image', 'slot' => 'left', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[3]['id'], 'type' => 'image', 'slot' => 'right', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[3]['id'], 'type' => 'image', 'slot' => 'center-left', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[3]['id'], 'type' => 'image', 'slot' => 'center-right', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[4]['id'], 'type' => 'text', 'slot' => 'content', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],            
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[5]['id'], 'type' => 'image', 'slot' => 'left', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[5]['id'], 'type' => 'image', 'slot' => 'right', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[6]['id'], 'type' => 'text', 'slot' => 'content', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[7]['id'], 'type' => 'text', 'slot' => 'content', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId],
            ['id' => Uuid::randomBytes(), 'locked' => 0, 'cms_block_id' => $blocks[8]['id'], 'type' => 'product-slider', 'slot' => 'productSlider', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), 'version_id' => $versionId]
        ];
        $productIds = $this->getShopProductIds(8);
        $bestproductIds = $this->getBestSellerProductIds(8);
        $shop_url = getenv('APP_URL');          
        // Slot translation and configuration data
        $slotTranslationData = [
            [
                'cms_slot_id' => $slots[0]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'url' => ['value' => null, 'source' => 'static'],
                    'newTab' => ['value' => false, 'source' => 'static'],
                    'media' => ['value' => $bcMediaFiles[0]['id'], 'source' => 'static'],
                    'minHeight' => ['value' => '760px', 'source' => 'static'],
                    'displayMode' => ['value' => 'cover', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[1]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'title' => ['source' => 'static', 'value' => "New arrival"],
                    'border' => ['source' => 'static', 'value' => false],
                    'rotate' => ['value' => false, 'source'=> 'static'],
                    'products' => ['value' => [$productIds[0], $productIds[1], $productIds[2], $productIds[3]], 'source' => 'static'],
                    'boxLayout' => ['value' => 'standard', 'source'=> 'static'],
                    'elMinWidth'=> ['value' => '300px', 'source' => 'static'],
                    'navigation' => ['value' => true, 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[2]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'content' => ['value' => "<h2 style=\"text-align: center;\">Product categories</h2>\n<hr>\n<p style=\"text-align: center;\">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, \n sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, \n sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. \n Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>", 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[3]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'url' => ['value' => null, 'source' => 'static'],
                    'newTab' => ['value' => false, 'source' => 'static'],
                    'media' => ['value' => $bcMediaFiles[3]['id'], 'source' => 'static'],
                    'minHeight' => ['value' => '', 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[4]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'url' => ['value' => null, 'source' => 'static'],
                    'newTab' => ['value' => false, 'source' => 'static'],
                    'media' => ['value' => $bcMediaFiles[4]['id'], 'source' => 'static'],
                    'minHeight' => ['value' => '', 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[5]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'url' => ['value' => null, 'source' => 'static'],
                    'newTab' => ['value' => false, 'source' => 'static'],
                    'media' => ['value' => $bcMediaFiles[5]['id'], 'source' => 'static'],
                    'minHeight' => ['value' => '', 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[6]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'url' => ['value' => null, 'source' => 'static'],
                    'newTab' => ['value' => false, 'source' => 'static'],
                    'media' => ['value' => $bcMediaFiles[6]['id'], 'source' => 'static'],
                    'minHeight' => ['value' => '', 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[7]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'content' => ['value' => "<h2 style=\"text-align: center;\">Our offers</h2>\n<hr>\n<p style=\"text-align: center;\">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, \n sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, \n sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. \n Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>", 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[8]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'url' => ['value' => null, 'source' => 'static'],
                    'newTab' => ['value' => false, 'source' => 'static'],
                    'media' => ['value' => $bcMediaFiles[7]['id'], 'source' => 'static'],
                    'minHeight' => ['value' => '', 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[9]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'url' => ['value' => null, 'source' => 'static'],
                    'newTab' => ['value' => false, 'source' => 'static'],
                    'media' => ['value' => $bcMediaFiles[8]['id'], 'source' => 'static'],
                    'minHeight' => ['value' => '', 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[10]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
'content' => ['value' => "<h1 style=\"text-align: center;\">50% OFF</h1>\n<h2>PLUS FREE SHIPPING</h2><h3>48 HOUR FLASH SALE</h3><a href=\"".$shop_url."\"Kids-Games/\" class=\"btn soft_shop_now\" target=\"_blank\">SHOP NOW<span> > </span> </a>\n", 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[11]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'content' => ['value' => "<h1 style=\"text-align: center;\">80% OFF</h1>\n<h2>PLUS FREE SHIPPING</h2><h3>24 HOUR FLASH SALE</h3><a href=\"".$shop_url."\"Kids-Baby/\" class=\"btn soft_shop_now\" target=\"_blank\">SHOP NOW<span> > </span> </a>\n", 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],
            [
                'cms_slot_id' => $slots[12]['id'],
                'cms_slot_version_id' => $versionId,
                'language_id' => $languageEn,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'config' => json_encode([
                    'title' => ['source' => 'static', 'value' => 'Best seller'],
                    'border' => ['source' => 'static', 'value' => false],
                    'rotate' => ['value' => false, 'source'=> 'static'],
                    'products' => ['value' => [$bestproductIds[4], $bestproductIds[0], $bestproductIds[3], $bestproductIds[2], $bestproductIds[1]], 'source' => 'static'],
                    'boxLayout' => ['value' => 'standard', 'source'=> 'static'],
                    'elMinWidth'=> ['value' => '300px', 'source' => 'static'],
                    'navigation' => ['value' => true, 'source' => 'static'],
                    'displayMode' => ['value' => 'standard', 'source' => 'static'],
                    'verticalAlign' => ['value' => null, 'source' => 'static'],
                ]),
            ],            
        ];
        
        $slotTranslations = [];
        foreach ($slotTranslationData as $slotTranslationDatum) {
            $slotTranslationDatum['language_id'] = $languageEn;
            $slotTranslations[] = $slotTranslationDatum;
            if ($languageDe) {
                $slotTranslationDatum['language_id'] = $languageDe;
                $slotTranslations[] = $slotTranslationDatum;
            }
        }

        foreach ($slots as $slot) {
            $this->connection->insert('cms_slot', $slot);
        }

        foreach ($slotTranslations as $translation) {
            $this->connection->insert('cms_slot_translation', $translation);
        }
    }
    /**
     * Remove Cms page entires and configuration
     * @param $connection object
     */
    private function removeBcCmsPage(Connection $connection): void
    {
        $bcCmsPageId = $this->getBcCmsPageId($connection);
        if ($bcCmsPageId === null) {
            return;
        }
        $sectionId = $connection->fetchAll(
            '
            SELECT id
            FROM cms_section
            WHERE cms_page_id = :cms_page_id',
            ['cms_page_id' => $bcCmsPageId]
        );
        $blockIds = array();
        foreach ($sectionId as $skey => $svalue) {
            $blockId = $connection->fetchAll(
                '
				SELECT id
				FROM cms_block
				WHERE cms_section_id = :cms_section_id',
                ['cms_section_id' => $svalue['id']]
            );
            $blockIds[] = $blockId;
        }
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($blockIds as $blockVal) {
            foreach ($blockVal as $bkey => $bvalue) {
                $slotId = $connection->fetchColumn(
                    '
					SELECT id
					FROM cms_slot
					WHERE cms_block_id = :blockId',
                    ['blockId' => $bvalue['id']]
                );
                if ($slotId !== null) {
                    $this->connection->executeQuery('DELETE FROM `cms_slot_translation` WHERE `cms_slot_id` = :slotId', ['slotId' => $slotId]);
                    $this->connection->executeQuery('DELETE FROM `cms_slot` WHERE `cms_block_id` = :blockId', ['blockId' => $bvalue['id']]);
                }
                $this->connection->executeQuery('DELETE FROM `cms_block` WHERE `id` = :blockId', ['blockId' => $bvalue['id']]);
            }
        }
        
        foreach ($sectionId as $skey => $svalue) {
            $this->connection->executeQuery('DELETE FROM `cms_section` WHERE `id` = :sectionId', ['sectionId' => $svalue['id']]);
        }
        
        if ($bcCmsPageId !== null) {
            $this->connection->executeQuery('DELETE FROM `cms_page_translation` WHERE `cms_page_id` = :cmsPageId', ['cmsPageId' => $bcCmsPageId]);
            $this->connection->executeQuery('DELETE FROM `cms_page` WHERE `id` = :pageId', ['pageId' => $bcCmsPageId]);
        }
        
        foreach ($this->bc_media_files_ary as $bcMedianame) {
            $this->connection->executeQuery('DELETE FROM `media` WHERE `file_name` = :fileName', ['fileName' => $bcMedianame]);
        }
        
        // Rename the default folder name to dynamcally generated media folders
        
        foreach (glob(__DIR__ . '/Resources/media/*/*.jpg') as $file) {
            if ($this->bc_media_files_ary[0] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[0]['id']);
            }
            if ($this->bc_media_files_ary[1] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[1]['id']);
            }
            if ($this->bc_media_files_ary[2] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[2]['id']);
            }
            if ($this->bc_media_files_ary[3] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[3]['id']);
            }
            if ($this->bc_media_files_ary[4] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[4]['id']);
            }
            if ($this->bc_media_files_ary[5] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[5]['id']);
            }
            if ($this->bc_media_files_ary[6] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[6]['id']);
            }
            if ($this->bc_media_files_ary[7] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[7]['id']);
            }
            if ($this->bc_media_files_ary[8] == pathinfo($file, PATHINFO_FILENAME)) {
                rename($this->mpath.basename(dirname($file)), $this->mpath.$this->bc_default_folder_name[8]['id']);
            }
        }
        $this->connection->executeQuery('DELETE FROM `theme` WHERE `technical_name` = :themeName', ['themeName' => 'BrandCrockSoftTheme']);
        foreach ($this->bc_theme_media_files_ary as $bcThemeMedianame) {
            $this->connection->executeQuery('DELETE FROM `media` WHERE `file_name` = :fileName', ['fileName' => $bcThemeMedianame]);
        }
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1;');
    }
    /**
    * Get Cms page id
    * @param $connection object
    * return $result string
    */
    private function getBcCmsPageId(Connection $connection): ?string
    {
        $result = $connection->fetchColumn(
            'SELECT cms_page_id
            FROM cms_page_translation
            INNER JOIN cms_page ON cms_page.id = cms_page_translation.cms_page_id
            WHERE name = :name',
            ['name' => 'SoftTheme Home Page']
        );
        return $result === false ? null : (string) $result;
    }     
    /**
     * Get Default media folser ids
     * @param $entity string
     * return $result string
     */
    private function getDefaultFolderIdForEntity(string $entity)
    {
        $result = $this->connection->fetchColumn('
            SELECT LOWER(HEX(`media_folder`.`id`))
            FROM `media_default_folder`
            JOIN `media_folder` ON `media_default_folder`.`id` = `media_folder`.`default_folder_id`
            WHERE `media_default_folder`.`entity` = :entity;
        ', ['entity' => $entity]);

        if (!$result) {
            throw new \RuntimeException('No default folder for entity "' . $entity . '" found, please make sure that basic data is available by running the migrations.');
        }
        return (string) $result;
    }
    /**
     * Get German language id
     * @param $connection string
     * return $result string
     */
    private function getLanguageDeId(Connection $connection): ?string
    {
        $result = $connection->fetchColumn(
            '
            SELECT lang.id
            FROM language lang
            INNER JOIN locale loc ON lang.translation_code_id = loc.id
            AND loc.code = "de-DE"'
        );
        if ($result === false || Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM) === $result) {
            return null;
        }
        return (string) $result;
    }
    /**
     * Get Product ids
     * @param $limit int
     * return $productIds array
    */
    private function getShopProductIds($limit): array
    {
        $sql = 'SELECT LOWER(HEX(id)) as id FROM product ORDER BY created_at DESC LIMIT '.$limit;
        $productIds = $this->connection->fetchAll($sql);
        return array_column($productIds, 'id');
    }
    /**
     * Get Best seller Product ids
     * @param $limit int
     * return $bestProductIds array
    */
    private function getBestSellerProductIds($limit): array
    {
        $sql = 'SELECT LOWER(HEX(id)) as id FROM product ORDER BY created_at ASC LIMIT '.$limit;
        $bestProductIds = $this->connection->fetchAll($sql);
        return array_column($bestProductIds, 'id');
    }
    /**
     * Get Store Brands Media Id
     * @param $limit int
     * return $storeBrandsIds array
    */
    private function getStoreBrandsId(): array
    {
        $sql = 'SELECT LOWER(HEX(media_id)) as id FROM `product_manufacturer` WHERE media_id IS NOT NULL';
        $storeBrandsIds = $this->connection->fetchAll($sql);
        $arrStoreBrandsId = array_column($storeBrandsIds, 'id');
        $arrMediaUrl = array();
        foreach($arrStoreBrandsId as $media_id) {
            $arrMediaUrl[] = $media_id;
        }
        return $arrMediaUrl;
    }
}