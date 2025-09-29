<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Proposal_pdf extends App_pdf
{
    protected $proposal;

    private $proposal_number;

    // Watermark text for proposal PDFs; can be customized via hook 'proposal_pdf_watermark_text'
    protected $watermarkText = 'PROFORMA INVOICE';

    public function __construct($proposal, $tag = '')
    {
        if ($proposal->rel_id != null && $proposal->rel_type == 'customer') {
            $this->load_language($proposal->rel_id);
        } else if ($proposal->rel_id != null && $proposal->rel_type == 'lead') {
            $CI = &get_instance();

            $this->load_language($proposal->rel_id);
            $CI->db->select('default_language')->where('id', $proposal->rel_id);
            $language = $CI->db->get('leads')->row()->default_language;

            load_pdf_language($language);
        }

        $proposal                = hooks()->apply_filters('proposal_html_pdf_data', $proposal);
        $GLOBALS['proposal_pdf'] = $proposal;

        parent::__construct();

        $this->tag      = $tag;
        $this->proposal = $proposal;

        $this->proposal_number = format_proposal_number($this->proposal->id);

        $this->SetTitle($this->proposal_number);
        $this->SetDisplayMode('default', 'OneColumn');

        # Don't remove these lines - important for the PDF layout
        $this->proposal->content = $this->fix_editor_html($this->proposal->content);


    }

    public function prepare()
    {
        $number_word_lang_rel_id = 'unknown';

        if ($this->proposal->rel_type == 'customer') {
            $number_word_lang_rel_id = $this->proposal->rel_id;
        }

        $this->with_number_to_word($number_word_lang_rel_id);

        $total = '';
        if ($this->proposal->total != 0) {
            $total = app_format_money($this->proposal->total, get_currency($this->proposal->currency));
            $total = _l('proposal_total') . ': ' . $total;
        }

        $this->set_view_vars([
            'number'       => $this->proposal_number,
            'proposal'     => $this->proposal,
            'total'        => $total,
            'proposal_url' => site_url('proposal/' . $this->proposal->id . '/' . $this->proposal->hash),
        ]);

        return $this->build();
    }

    public function Header()
    {
        // Keep default header hooks; do NOT draw watermark here to avoid being behind content
        parent::Header();
    }

    public function Footer()
    {
        // Draw watermark after the main content so it overlays the page
        $wmText = hooks()->apply_filters('proposal_pdf_watermark_text', $this->watermarkText, $this->proposal);
        if (!empty($wmText)) {
            $this->drawWatermark($wmText);
        }
        // Keep default footer (page numbers, hooks)
        parent::Footer();
    }

    protected function drawWatermark($text)
    {
        // Save current settings
        $currentFont = $this->get_font_name();
        $currentSize = $this->get_font_size();
        $this->SetTextColor(200, 200, 200);
        $this->SetFont($currentFont, 'B', 45);

        // Rotate and center text
        $pageWidth  = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();
        $this->StartTransform();
        $this->Rotate(65, $pageWidth / 2, $pageHeight / 2);
        $this->SetXY(0, $pageHeight / 2 - 10);
        $this->Cell($pageWidth, -100, mb_strtoupper($text, 'UTF-8'), 0, 0, 'C');
        $this->StopTransform();

        // Restore defaults
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($currentFont, '', $currentSize);
    }

    protected function type()
    {
        return 'proposal';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_proposalpdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/proposalpdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
