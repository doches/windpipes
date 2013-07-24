<?php

// Convenience crap for checking whether an array is associative or numeric.
// http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-numeric
function is_assoc($array) {
  return (bool)count(array_filter(array_keys($array), 'is_string'));
}

class XMLResponse {
    var $opts;

    function __construct() {
        $this->opts = Array();
    }

    function set($key, $value) {
        $this->opts[$key] = $value;
    }

    function output() {
        header("Content-type: application/xml");

        // Create XML
        $this->xml = new DomDocument("1.0");
        $root = $this->xml->createElement('response');
        foreach ($this->opts as $key => $value) {
            if (is_array($value))
                Response::createNestedChild($this->xml, $root, $key, $value);
            else
                Response::createTextChild($this->xml, $root, $key, $value);
        }
        $this->xml->appendChild($root);

        // Output XML response
        echo $this->xml->saveXML();
    }

    private function createTextChild($doc, $parent, $element, $text) {
        $node = $doc->createElement($element);
        $node->appendChild($doc->createTextNode($text));
        $parent->appendChild($node);
    }

    private function createNestedChild($doc, $parent, $element, $array) {
        if (isset($array['__xml_tag'])) {
            $element = $array['__xml_tag'];
            unset($array['__xml_tag']);
        }
        $use_attributes = false;
        if (isset($array['__xml_attr'])) {
            $use_attributes = true;
            unset($array['__xml_attr']);
        }

        $node = $doc->createElement($element);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                Response::createNestedChild($doc, $node, $key, $value);
            } else {
                if ($use_attributes) {
                    $node->setAttribute($key, $value);
                } else {
                    Response::createTextChild($doc, $node, $key, $value);
                }
            }
        }
        $parent->appendChild($node);
    }

    // Note: I don't actually use CDATA encoding anywhere, but I'm leaving this
    // in place so I don't have to look up the ridiculous formatting should I
    // start to one day.
    static function cdata($data) {
        return "<![CDATA[$data]]>";
    }
}

class JSONResponse extends XMLResponse {
    function clean($dict) {
        foreach($dict as $key => $value) {
            if (startsWith($key, "__xml")) {
                unset($dict[$key]);
            } else if (is_array($value)) {
                $value = $this->clean($value);
                $dict[$key] = $value;
            }
        }

        return $dict;
    }

    function output() {
        header("Content-type: application/json");

        $this->opts = $this->clean($this->opts);

        echo json_encode($this->opts);
    }

    static function cdata($data) {
        // TODO: implement JSON escaping
        return $data;
    }
}

?>