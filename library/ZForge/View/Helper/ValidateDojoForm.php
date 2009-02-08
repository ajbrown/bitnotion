<?php

/**
 * Credit:http://peacocksuit.com/blog/2008/11/10/zend_dojo_form-and-form-validation-on-the-client-side/
 */
require_once 'Zend/View/Helper/Abstract.php';
require_once 'Zend/View/Helper/Interface.php';


class ZForge_View_Helper_ValidateDojoForm extends Zend_View_Helper_Abstract
{
    public $view;

    public function setView( Zend_View_Interface $view )
    {
        $this->view = $view;
    }

    /**
     * Validate dojo enabled form onSubmit.
     *
     * @param  string $formId
     * @return void
     */
    public function ValidateDojoForm($formId)
    {
        $this->view->headScript()->captureStart(); ?>
        function validateForm() {
            var form = dijit.byId("<?php echo $formId; ?>");
            if (!form.validate()) {
                return false;
            }
            return true;
        }
        dojo.addOnLoad(function () {
            dojo.connect(dijit.byId("<?php echo $formId; ?>"), "onSubmit", "validateForm");
        });
        <?php $this->view->headScript()->captureEnd();
    }
}