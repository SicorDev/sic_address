<?php
namespace SICOR\SicAddress\ViewHelpers\Format;

/**
 * Use this view helper to convert a string to lower or uppercase.
 *
 * = Examples =
 *
 * <code title="Upper case">
 * <f:format.case case="upper">Some text here</f:format.case>
 * </code>
 * <output>
 * SOME TEXT HERE
 * </output>
 *
 * <code title="Lower case">
 * <f:format.case case="lower">Some text here</f:format.case>
 * </code>
 * <output>
 * some text here
 * </output>
 *
 * @api
 */
class CaseViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Render the converted text
     *
     * @param string $case "upper" or "lower"
     * @return string converted text
     * @api
     */
    public function render($case) {
        $stringToConvert = $this->renderChildren();
        switch ($case) {
            case 'upper':
                return strtoupper($stringToConvert);
            case 'lower':
                return strtolower($stringToConvert);
            case 'capital':
                return ucfirst($stringToConvert);
            default:
                return $stringToConvert;
        }
    }
}


?>