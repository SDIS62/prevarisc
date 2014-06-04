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
            echo "<img $attribs src='" . $src . ( file_exists($_SERVER['DOCUMENT_ROOT'].$src.$id.".jpg") ? $id : "default" ) . ".jpg' alt='Avatar' />";
        }
    }
