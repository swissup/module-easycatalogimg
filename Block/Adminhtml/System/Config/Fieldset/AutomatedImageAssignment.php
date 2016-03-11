<?php
namespace Swissup\Easycatalogimg\Block\Adminhtml\System\Config\Fieldset;

use Magento\Framework\Data\Form\Element\AbstractElement;

class AutomatedImageAssignment extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(AbstractElement $element)
    {
        $url  = $this->getUrl('easycatalogimg/category/assignImage');
        $runText = __("Run");
        return <<<HTML
<tr>
    <td colspan="100">
        <div class="button-container">
            <button id="run-image-assignment" class="button action-configure" type="button"><span>$runText</span></button>
        </div>
        <script type="text/javascript">
            require([
                'jquery',
                'imageAssignment'
            ], function ($, imageAssignment) {
                imageAssignment.init("$url", '#run-image-assignment');
            });
        </script>
    </td>
</tr>
HTML;
    }
}
