<?php
namespace App;

class Form
{

    public $title;
    public $id;
    public $description;
    public $admin_name;
    public $admin_mail;
    public $format;
    public $end_date;
    public $type;

    /**
     * Tells if users can modify their choices.
     */
    public $editable;

    /**
     * If true, notify poll administrator when new vote is made.
     */
    public $receiveNewVotes;

    /**
     * If true, notify poll administrator when new comment is posted.
     */
    public $receiveNewComments;

    /**
     * Value max value
     */
    public $valueMax;

    /**
     * Does poll use value max?
     */
    public $useValueMax;

    /**
     * If true, only the poll maker can see the poll's results
     * @var boolean
     */
    public $hidden;

    /**
     * If true, the author want to customize the URL
     * @var boolean
     */
    public $use_customized_url;

    /**
     * If true, a password will be needed to access the poll
     * @var boolean
     */
    public $use_password;

    /**
     * The password needed to access the poll, hashed. Only used if $use_password is set to true
     * @var string
     */
    public $password_hash;

    /**
     * If true, the polls results will be also visible for those without password
     * @var boolean
     */
    public $results_publicly_visible;

    /**
     * List of available choices
     */
    private $choices;

    public function __construct(){
        $this->editable = config('laradate.EDITABLE_BY_ALL');
        $this->clearChoices();
    }

    public function clearChoices() {
        $this->choices = array();
    }

    public function addChoice(Choice $choice)
    {
        $this->choices[] = $choice;
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function sortChoices()
    {
        usort($this->choices, array('App\Choice', 'compare'));
    }

}
