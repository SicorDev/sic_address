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
class CaseViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('case', 'string', '"upper" or "lower"');
    }

    /**
     * Render the converted text
     *
     * @return string converted text
     * @api
     */
    public function render() {
        $case = $this->arguments['case'];
        
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
