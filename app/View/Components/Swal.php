namespace App\View\Components;

use Illuminate\View\Component;

class Swal extends Component
{
    public $title;
    public $text;
    public $icon;
    public $confirmButtonText;
    public $cancelButtonText;

    public function __construct($title = '', $text = '', $icon = 'warning', $confirmButtonText = 'Yes', $cancelButtonText = 'No')
    {
        $this->title = $title;
        $this->text = $text;
        $this->icon = $icon;
        $this->confirmButtonText = $confirmButtonText;
        $this->cancelButtonText = $cancelButtonText;
    }

    public function render()
    {
        return view('components.swal');
    }
}
