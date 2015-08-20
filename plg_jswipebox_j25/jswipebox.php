<?php
/**
* @package	Joomla.Plugin
* @subpackage	Content.jswipebox
 * 
* @copyright	Copyright (C) Shaking Web. All rights reserved.
* @license	GNU/GPL.
* @author 	Shaking Web (http://www.shakingweb.com) 
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentJswipebox extends JPlugin{
    
    /**
     * Constructor
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     * @since       1.6
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        // Load the plugin language file the proper way
        JPlugin::loadLanguage('plg_content_', JPATH_ADMINISTRATOR);
    }
    
    function onContentPrepare($context, &$article, &$params, $page)
    {        
        if(strpos($article->text, "[jswipebox]") !== false){
            $inicio = strpos($article->text, "[jswipebox]") + 11;
            $fin = strpos($article->text, "[/jswipebox]");
            $images = (substr($article->text, $inicio, ($fin - $inicio)));
//            $result = array();
            preg_match_all('/<img(.*)src(.*)=(.*)"(.*)"/U', $images, $result_src);
            preg_match_all('/<img(.*)title(.*)=(.*)"(.*)"/U', $images, $result_title);
//            print_r($result_title);
            $src_images = $result_src[4];
            $title_images = $result_title[4];
            $width = $this->params->get('width', '120');
            $height = $this->params->get('height', '90');
            
            $jswipebox_images = "<div class=\"wrap small-width\" style=\"visibility: visible !important;\"><div id=\"try\"></div>
                                <ul id=\"box-container\">";
            for($i = 0; $i < count($src_images); $i++){
                $jswipebox_images .= "<li class=\"box\">
					<a href=\"".$src_images[$i]."\" class=\"swipebox\" title=\"".$title_images[$i]."\">
						<img src=\"".$src_images[$i]."\" alt=\"image\" width=\"$width\" height=\"$height\">
					</a>
				</li>";
            }
            $jswipebox_images .= "</ul></div>";
            
            $document = &JFactory::getDocument();
            $document->addStyleSheet("plugins/content/jswipebox/assets/src/css/swipebox.css?v=1.0.6");
            $css = "ul#box-container li.box a img{
    width: ".$width."px !important;
    height: ".$height."px !important;
}";
            $document->addStyleDeclaration($css);
            $document->addScript("plugins/content/jswipebox/assets/lib/ios-orientationchange-fix.js");
            $document->addScript("plugins/content/jswipebox/assets/lib/jquery-2.1.0.min.js");
            $document->addScript("plugins/content/jswipebox/assets/src/js/jquery.swipebox.js");
            $document->addScriptDeclaration(" jQuery.noConflict();
                    $( document ).ready(function() {
                        /* Basic Gallery */
                        $('.swipebox').swipebox();
                    });
                ");
            $article->text = str_replace("[jswipebox]".$images."[/jswipebox]", $jswipebox_images."<br/>", $article->text);
        }
    }
    
}