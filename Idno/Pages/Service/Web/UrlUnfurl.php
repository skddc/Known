<?php

namespace Idno\Pages\Service\Web {

    class UrlUnfurl extends \Idno\Common\Page {

        function getContent() {

            $this->gatekeeper(); // Gatekeeper to ensure this service isn't abused by third parties
            //$this->xhrGatekeeper();

            $url = trim($this->getInput('url'));
            $objid = $this->getInput('object_id');
            $forcenew = $this->getInput('forcenew', false);

            \Idno\Core\Idno::site()->template()->setTemplateType('json');
            header('Content-type: application/json');

            if (empty($url))
                throw new \RuntimeException("You need to specify a working URL");

            $object = null;
            if (!empty($objid)) {
                $object = \Idno\Common\Entity::getByID($objid);
            }

            $key = 'unfurl-data-' . md5($url);

            if (!$forcenew) {
                if (!empty($object->$key)) {
                    echo json_encode($object->$key);
                    exit;
                }
            }

            $unfurled = \Idno\Core\Url::unfurl($url);
            if (empty($unfurled))
                throw new \RuntimeException("Url $url could not be unfurled");

            if (!empty($object)) {
                $object->$key = $urnfurled;
                $object->save();
            }

            // Pre-render (for javascript)
            $template = new \Idno\Core\Template();
            $template->setTemplateType('default');
            $unfurled['rendered'] = $template->__(['unfurled-url' => $unfurled])->draw('content/unfurledurl');

            echo json_encode($unfurled);
        }

    }

}