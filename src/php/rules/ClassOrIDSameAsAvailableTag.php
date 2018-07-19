<?php
function ClassOrIDSameAsAvailableTag()
{
    // GLOBALS
    global $_ft_dom_;
    $sug = (object) [
        'title' => 'Suspicious class names or ids found',
        'description' => 'Class names or id attribute values matching HTML5 tags were found. Semantic HTML is preferred, so use the HTML5 element instead. For example, <code>' . htmlentities('<section>...</section>') . '</code> not <code>' . htmlentities('<div class="section">...</div>') . '</code><br>',
        'weight' => 10,
        'category' => ['content'],
    ];
    
    $poorly_designed_catchall = 0;
    $poorly_designed_catchall_element_array = array();

    $code = array('');
    $code[0] = [];
    $code[1] = 0; 
    
    $html5_elements_suspicious = "navigation navbar headerbar footerbar menubar title heading abbr address article aside blockquote button canvas caption cite code col colgroup command datagrid datalist details em fieldset figcaption figure footer header hgroup hr label legend li menu meter nav noscript object ol optgroup output p param pre progress section small source span strong sub summary sup tbody tfoot thead time ul var video ";
    $html5_elements_array = explode(' ',$html5_elements_suspicious);
    
    //only search divs for now. 
    $elements = $_ft_dom_->getElementsByTagName('div');
    			
    //outer foreach will only loop once.			
    foreach($elements as $element) {
        if(($element->hasAttribute('class') && in_array($element->getAttribute('class'),$html5_elements_array))) {
            $code[1]++;
            $code[0][] = '&lt;' . $element->tagName . ' class="' . $element->getAttribute('class') . '"&gt;';
            continue;
        }
        
        if(($element->hasAttribute('id') && in_array($element->getAttribute('id'),$html5_elements_array))) {
            $code[1]++;					
            $code[0][] = '&lt;' . $element->tagName . ' id="' . $element->getAttribute('id') . '"&gt;';
            continue;
        }
    }
                                
    if (count($code[0])) {
        $instances = '';
        foreach($code[0] as $instance) {
            $instances .= '<br><code>' . $instance . '</code>';
        }
        $sug->description .= $instances;
        return $sug;
    }				

    return false;
}
?>
    
