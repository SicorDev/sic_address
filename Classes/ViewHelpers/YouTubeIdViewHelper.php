<?php

namespace SICOR\SicAddress\ViewHelpers;

/**
 * A view helper for rendering the YouTube Id from an url
 *
 * = Examples =
 *
 * <code>
 * {nc:youTubeId(url: 'https://www.youtube.com/watch?v=zpOVYePk6mM')}
 * </code>
 * <output>
 * zpOVYePk6mM
 * </output>
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class YouTubeIdViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('url', 'string', 'YouTube url', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string youtube id
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $url = $arguments['url'];
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        $youtube_id = $match[1];

        return $youtube_id;
    }
}
