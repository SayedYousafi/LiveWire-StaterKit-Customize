<?php
namespace App\Livewire;

use App\Models\Parents;
use App\Models\ShortDescription;
use Livewire\Component;

class ItemDescription extends Component
{
    public $parent_id = 263, $shortDescs = [];
    public $rows      = [['type' => 0, 'value' => '', 'value2' => '']];

    protected $listeners = ['checkLastRow'];

    public function mount($parent_id)
    {
        $this->parent_id = $parent_id;
        $this->loadExistingRows();
    }

    public function checkLastRow($index)
    {
        if ($index === array_key_last($this->rows) && count($this->rows) < 6) {
            $this->rows[] = ['type' => 0, 'value' => '', 'value2' => ''];
        }
    }

    public function loadExistingRows()
    {
        $existingRows = ShortDescription::where('parent_id', $this->parent_id)->get();

        if ($existingRows->isNotEmpty()) {
            $this->rows       = $existingRows->unique('type')->map(fn($row) => ['type' => $row->type, 'value' => $row->value, 'value2' => $row->value2])->toArray();
            $this->shortDescs = $existingRows;
        }
    }

    public function saveRows()
    {
        ShortDescription::where('parent_id', $this->parent_id)->delete();

        $items = Parents::with(['items:id,ean,item_name,model,parent_id'])
            ->where('id', $this->parent_id)
            ->get(['id', 'name_de'])
            ->toArray();

        foreach ($items as $parent) {
            if (! isset($parent['items'])) {
                continue;
            }

            foreach ($parent['items'] as $item) {
                foreach ($this->rows as $row) {
                    // Only create if type is not 0
                    if ($row['type'] != 0) {
                        ShortDescription::create([
                            'type'        => $row['type'],
                            'value'       => $row['value'],
                            'value2'      => $row['value2'],
                            'item_id'     => $item['id'],
                            'ean'         => $item['ean'],
                            'item_name'   => $item['item_name'],
                            'model'       => $item['model'] ?? null,
                            'parent_id'   => $this->parent_id,
                            'parent_name' => $parent['name_de'],
                        ]);
                    }
                }
            }
        }

        $this->reset('rows');
        $this->rows = [['type' => 0, 'value' => '', 'value2' => '']];
        $this->loadExistingRows();
        session()->flash('success', 'Short description created and saved successfully!');
    }

    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $output = fopen('php://output', 'w');
            fprintf($output, "\xEF\xBB\xBF"); // Add UTF-8 BOM
            fputcsv($output, ["EAN", "Short Description EN", "Short Description DE"]);

            $shortDescs   = ShortDescription::all();
            $groupedItems = $shortDescs->groupBy('ean');

            foreach ($groupedItems as $ean => $items) {
                // $filteredItems = $items->filter(fn($item) => ! ($item->type == 6 && is_null($item->model)));
                $filteredItems = $items->filter(fn($item) => !($item->type == 6 && empty($item->model)));
                $fullTextEn = $filteredItems->map(fn($item) => match ($item->type) {
                    1 => "<p>{$item->value}</p>",
                    2 => "<p>{$item->value} <strong><a href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'>{$item->parent_name} Inquiry</a></strong></p>",
                    3 => "<p><a href='https://data.gtech-shop.de/CAD/" . str_replace(' ', '_', $item->item_name) . ".stp'><img src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download {$item->value} \"{$item->item_name}.stp\"</strong></a></p>",
                    4 => "<p><a href='https://data.gtech-shop.de/Datasheets/" . str_replace(' ', '_', $item->parent_name) . ".pdf' target='_blank'><img src='https://data.gtech-shop.de/data/Icons/Datenblatt_GT_Icon.jpg' /> <strong>Download {$item->value} {$item->parent_name}.pdf</strong></a></p>",
                    5 => "<p><a href='https://data.gtech-shop.de/Datasheets/{$item->value}.pdf' target='_blank'><img src='https://data.gtech-shop.de/data/Icons/Datenblatt_Norm_Icon.jpg' /> <strong>Download {$item->value}.pdf</strong></a></p>",
                    6 => "<p><a href='https://data.gtech-shop.de/CAD/{$item->model}.stp'><img alt='' src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download \"{$item->model}.stp\"</strong></a> <a href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'><strong>Inquire other {$item->parent_name} CAD files</strong></a></p>",
                })->implode('');
                
                $fullTextDe = $filteredItems->map(fn($item) => match ($item->type) {
                    1 => "<p>{$item->value2}</p>",
                    2 => "<p>{$item->value2} <strong><a href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'>{$item->parent_name} Inquiry</a></strong></p>",
                    3 => "<p><a href='https://data.gtech-shop.de/CAD/" . str_replace(' ', '_', $item->item_name) . ".stp'><img src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download {$item->value2} \"{$item->item_name}.stp\"</strong></a></p>",
                    4 => "<p><a href='https://data.gtech-shop.de/Datasheets/" . str_replace(' ', '_', $item->parent_name) . ".pdf' target='_blank'><img src='https://data.gtech-shop.de/data/Icons/Datenblatt_GT_Icon.jpg' /> <strong>Download {$item->value2} {$item->parent_name}.pdf</strong></a></p>",
                    5 => "<p><a href='https://data.gtech-shop.de/Datasheets/{$item->value2}.pdf' target='_blank'><img src='https://data.gtech-shop.de/data/Icons/Datenblatt_Norm_Icon.jpg' /> <strong>Download {$item->value2}.pdf</strong></a></p>",
                    6 => "<p><a href='https://data.gtech-shop.de/CAD/{$item->model}.stp'><img alt='' src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download \"{$item->model}.stp\"</strong></a> <a href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'><strong>Inquire other {$item->parent_name} CAD files</strong></a></p>",
                })->implode('');               

                fputcsv($output, [$ean, $fullTextEn, $fullTextDe]);
            }

            fclose($output);
        }, 'short_descriptions.csv');
    }

    public function render()
    {
        return view('livewire.item-description', [
            'pItems'     => Parents::findOrFail($this->parent_id),
            'shortDescs' => ShortDescription::where('parent_id', $this->parent_id)->get(),

        ]);
    }
}
