<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Exception\LocalizedException;

class CommandProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    const EMPTY_VALUE = '';

    /**
     * @var CommandInterface[]
     */
    private $commands = [];

    /**
     * @var string
     */
    private $captionLabel;

    public function __construct(
        string $captionLabel,
        array $commands = []
    ) {
        foreach ($commands as $tool) {
            $this->commands[$tool->getType()] = $tool;
        }
        $this->captionLabel = $captionLabel;
    }

    public function get($key): ?CommandInterface
    {
        if (!isset($this->commands[$key])) {
            throw new LocalizedException(__('Tool `%1` doesn\'t exist', $key));
        }

        return $this->commands[$key];
    }

    use ToOptionArrayTrait;

    public function toArray(): array
    {
        $result = [];
        if (!empty($this->captionLabel)) {
            $result[self::EMPTY_VALUE] = __($this->captionLabel);
        }
        foreach ($this->commands as $tool) {
            $result[$tool->getType()] = $tool->getName();
        }

        return $result;
    }
}
