<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class PolicyParser
{
    private $logService;

    public function __construct($log_service)
    {
        $this->logService = $log_service;
    }

    public function parse($post_id)
    {
        $post   = get_post($post_id);
        $output = apply_filters('the_content', $post->post_content);
        $html   = str_get_html($output);

        $elements   = [];
        $htmlResult = null;


        if ($html != null) {
            $htmlResult = $html->find('p,form,h1,h2,h3,h4,h5,h6,h7,table,ul,ol');

            // foreach($htmlResult as $key => $dom) {
            //     if ($dom->tag == 'table') {
            //         $htmlResult[$key] = preg_replace("~</?p[^>]*>~", "", $dom);
            //     }

            //     if ($dom->parent()->tag === 'td') {
            //         $htmlResult[$key] = null;
            //     }
            // }
        }
        
        if ($htmlResult == null) {
            return;
        }
   
        foreach ($htmlResult as $key=>$domElement) {

            if ($domElement->tag === 'table'){

                $element    = $domElement->outertext;
                $wrapElement = preg_replace('/^(<table .*?<\/table>)$/', '<p>$1</p>', $element);

                $elements[] = array(
                    'type'     => 'ptag',
                    'with_tag' => $wrapElement,
                    'no_tag'   => $element
                );
            }

            if($domElement->tag === 'ul' && !strpos(json_encode($domElement->parent()->class), 'wpforms-field')){
               
                $element = $domElement->outertext;
                $wrapElement = preg_replace('/^(<ul.*?>.*?<\/ul>)$/', '<p>$1</p>', $element);

                $elements[] = array(
                    'type'     => 'ptag',
                    'with_tag' => $wrapElement,
                    'no_tag'   => $element
                );
            }

            if($domElement->tag === 'ol'){
               
                $element = $domElement->outertext;
                $wrapElement = preg_replace('/^(<ol.*?>.*?<\/ol>)$/', '<p>$1</p>', $element);

                $elements[] = array(
                    'type'     => 'ptag',
                    'with_tag' => $wrapElement,
                    'no_tag'   => $element
                );
            }
            

            if ($domElement->tag === 'p' && $domElement->parent()->tag !== 'td') {
                if( $domElement->innertext !== '&nbsp;' && $domElement->parent()->tag !== 'li'){
                    $with_tag = $domElement->outertext;
                    $no_tag   =  $domElement->innertext;
                    //remove the div(form) if it occur first in the ptag
                    if (preg_match('/<p><div .*?<\/form><\/div>.*?<\/p>/', $with_tag)) {
                        $with_tag = preg_replace('/^(<p>)<div .*?<\/form><\/div>(.*?<\/p>)$/', '$1$2', $with_tag);
                    }
                    
                    if (preg_match('/<div .*?<\/form><\/div>.*?/', $no_tag)) {
                        $no_tag  = preg_replace('/^<div .*?<\/form><\/div>(.*?)$/', '$1', $no_tag);
                    }
                    //remove the div(form) if it occur last in the p tag
                    if (preg_match('/<p>.*?<div .*?<\/div><\/p>/', $with_tag)) {
                        $with_tag = preg_replace('/^(<p>.*?)<div .*?<\/div>(<\/p>)$/', '$1$2', $with_tag);
                    }
                    
                    if (preg_match('/.*?<div .*?<\/div>/', $no_tag)) {
                        $no_tag  = preg_replace('/^(.*?)<div .*?<\/div>$/', '$1', $no_tag);
                    }
                     
                    if (preg_match('/<strong><em>.*?<\/em><\/strong>/', $no_tag)) {
                        $no_tag = $domElement->plaintext;
                    }
    
                    if (preg_match('/<strong>.*?<\/strong>/', $no_tag)) {
                        $no_tag = $domElement->plaintext;
                    }
    
                    if (preg_match('/<em>.*?<\/em>/', $no_tag)) {
                        $no_tag = $domElement->plaintext;
                    }
    
                    $elements[] = array(
                        'type'     => 'ptag',
                        'with_tag' => $with_tag,
                        'no_tag'   => $no_tag
                    );
                }
            }

            if ($domElement->tag == 'h1' || $domElement->tag == 'h2' || $domElement->tag == 'h3' || $domElement->tag == 'h4' || $domElement->tag == 'h5' || $domElement->tag == 'h6') {
                
                if($domElement->parent()->tag !== 'li'){
                    if ($domElement->innertext != null) {
                        $no_tag   =  $domElement->innertext;
                    
                        if (preg_match('/<strong><em>.*?<\/em><\/strong>/', $no_tag)) {
                            $no_tag = $domElement->plaintext;
                        }
        
                        if (preg_match('/<strong>.*?<\/strong>/', $no_tag)) {
                            $no_tag = $domElement->plaintext;
                        }
        
                        if (preg_match('/<em>.*?<\/em>/', $no_tag)) {
                            $no_tag = $domElement->plaintext;
                        }
        
                        $elements[] = array(
                            'type'     => 'header',
                            'with_tag' => $domElement->outertext,
                            'no_tag'   => $no_tag,
                        );
                    }
                }
                
            }
 

            if ($domElement->tag == 'form') {
                //$checkBoxOptionValue = $domElement->find('ul', 0)->plaintext;
                $elements[] = array(
                    'type'              => 'form',
                    //get the first id attribute in the form
                    'wp_form_id'        => $domElement->attr['id'],
                    'form_title'        => $domElement->find('.wpforms-title', 0)->innertext,
                    'form_type'         => $domElement->find('.wpforms-field', 0)->outertext,
                    'form_description'  => $domElement->find('.wpforms-description', 0)->innertext,
                    'form'              => $domElement->outertext,
                );
            }
        }

        $has_read_required = has_shortcode( $post->post_content, 'readrequired');

        if($has_read_required) {
            $elements[] = array(
                'type'     => 'ptag',
                'with_tag' => "<p>[readrequired]</p>",
                'no_tag'   => "[readrequired]"
            );
        }

        return $elements;
    }
}
