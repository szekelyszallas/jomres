<?php
/**
 * Core file.
 *
 * @author Vince Wooll <sales@jomres.net>
 *
 * @version Jomres 9.8.21
 *
 * @copyright	2005-2016 Vince Wooll
 * Jomres (tm) PHP, CSS & Javascript files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly
 **/

// ################################################################
defined('_JOMRES_INITCHECK') or die('');
// ################################################################

class j06000show_property_main_image
{
    public function __construct($componentArgs)
    {
        // Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
        $MiniComponents = jomres_singleton_abstract::getInstance('mcHandler');
        if ($MiniComponents->template_touch) {
            $this->template_touchable = false;
            $this->shortcode_data = array(
                'task' => 'show_property_main_image',
                'info' => '_JOMRES_SHORTCODES_06000SHOW_PROPERTY_MAIN_IMAGE',
                'arguments' => array(0 => array(
                        'argument' => 'property_uid',
                        'arg_info' => '_JOMRES_SHORTCODES_06000SHOW_PROPERTY_MAIN_IMAGE_ARG_PROPERTY_UID',
                        'arg_example' => '1',
                        ),
                    ),
                );

            return;
        }
        $this->retVals = '';
        if (isset($componentArgs[ 'property_uid' ])) {
            $property_uid = (int) $componentArgs[ 'property_uid' ];
        } elseif (isset($_REQUEST['property_uid'])) {
            $property_uid = (int) $_REQUEST['property_uid'];
        } else {
            return;
        }

        if (!user_can_view_this_property($property_uid)) {
            return;
        }

        if (isset($componentArgs[ 'output_now' ])) {
            $output_now = $componentArgs[ 'output_now' ];
        } else {
            $output_now = true;
        }
		
		$siteConfig = jomres_singleton_abstract::getInstance('jomres_config_site_singleton');
        $jrConfig = $siteConfig->get();

        $output = array();
        $imagesArray = array();

        $jomres_media_centre_images = jomres_singleton_abstract::getInstance('jomres_media_centre_images');
        $jomres_media_centre_images->get_images($property_uid, array('property'));

        $imagesArray = $jomres_media_centre_images->images['property'][0];

		if ($jrConfig['plist_images_as_slideshow']) {
			$slideshowArgs = array();
			$slideshowArgs['property_uid'] = $property_uid;
			$slideshowArgs['height'] = 0.60;
			$slideshowArgs['lightbox'] = 'false';
			$slideshowArgs['autoplay'] = 'false';
			$slideshowArgs['thumbnails'] = 'false';
			$slideshowArgs['transition'] = 'fade';
			$slideshowArgs['showcounter'] = 'false';
			$slideshowArgs['link_to_property_details'] = true;
			$slideshowArgs['images'] = $imagesArray;
			$result = $MiniComponents->specificEvent('01060', 'slideshow', $slideshowArgs);
			$output[ 'SLIDESHOW' ] = $result['slideshow'];
		} else {
			$output[ 'SLIDESHOW' ] = '<a href="'.jomresURL(JOMRES_SITEPAGE_URL.'&task=viewproperty&property_uid='.$property_uid).'"><img src="'.$jomres_media_centre_images->images['property'][0][0]['medium'].'" class="responsive img-responsive" alt="property image"/></a>';
		}

        $pageoutput = array();
        $pageoutput[] = $output;
        $tmpl = new patTemplate();
        $tmpl->addRows('pageoutput', $pageoutput);
        $tmpl->setRoot(JOMRES_TEMPLATEPATH_FRONTEND);
        $tmpl->readTemplatesFromInput('show_property_main_image.html');
        $result = $tmpl->getParsedTemplate();

        if ($output_now) {
            echo $result;
        } else {
            $this->retVals = $result;
        }
    }

    // This must be included in every Event/Mini-component
    public function getRetVals()
    {
        return $this->retVals;
    }
}
