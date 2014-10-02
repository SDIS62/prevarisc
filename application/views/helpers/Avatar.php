<?php

    class View_Helper_Avatar extends Zend_View_Helper_HtmlElement
    {
        public function avatar($id, $size="small", $attribs = null)
        {
            // Attributs
            if ($attribs) {
                $attribs = $this->_htmlAttribs($attribs);
            } else {
                $attribs = '';
            }

            $src = DATA_PATH . "/uploads/avatars/$size/";
            $file_path = REAL_DATA_PATH . DS . "uploads" . DS . "avatars" . DS . $size . DS . $id.".jpg";
            echo "<img $attribs src='" . $src . ( file_exists($file_path) ? $id : "default" ) . ".jpg' alt='Avatar' />";
        }
    }
