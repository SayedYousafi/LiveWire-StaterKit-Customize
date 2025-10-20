@php
    /** @var array $actions */

    // If there are no actions, fill the 4 action cells with a single colspan.
    if (empty($actions)) {
        echo '<td colspan="4"></td>';
    } else {
        foreach ($actions as $btn) {
            $colspan = !empty($btn['colspan2']) ? ' colspan="2"' : '';

            // Static text cell
            if (!empty($btn['static'])) {
                echo '<td' . $colspan . '>' . e($btn['label']) . '</td>';
                continue;
            }

            // Build common <flux:button> attributes
            $attrs   = [];
            $attrs[] = 'size="sm"';
            if (!empty($btn['icon']))  $attrs[] = 'icon="'  . e($btn['icon'])  . '"';
            if (!empty($btn['class'])) $attrs[] = 'class="' . e($btn['class']) . '"';

            // Link-style button
            if (!empty($btn['href'])) {
                $attrs[] = 'as="a"';
                $attrs[] = 'href="' . e($btn['href']) . '"';
                if (!empty($btn['refresh'])) $attrs[] = 'wire:click="$refresh"';
                if (!empty($btn['confirm'])) $attrs[] = 'onclick="return confirm(' . json_encode($btn['confirm']) . ')"';

                $attrStr = implode(' ', $attrs);
                echo '<td' . $colspan . '><flux:button ' . $attrStr . '>' . e($btn['label']) . '</flux:button></td>';
                continue;
            }

            // Normal wire:click button
            if (!empty($btn['variant'])) $attrs[] = 'variant="' . e($btn['variant']) . '"';
            if (!empty($btn['click']))   $attrs[] = 'wire:click="' . $btn['click'] . '"'; // keep raw to preserve quotes

            $attrStr = implode(' ', $attrs);
            echo '<td' . $colspan . ' nowrap><flux:button ' . $attrStr . '>' . e($btn['label']) . '</flux:button></td>';
        }
    }
@endphp
