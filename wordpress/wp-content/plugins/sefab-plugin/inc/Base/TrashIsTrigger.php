<?php
/**
 * @package  Sefab Plugin
 */
namespace Inc\Base;
use \Inc\DbQuery\UpdateIsDeletePost;
use \Inc\DbQuery\UpdateIsDeleteParagraph;
use \Inc\DbQuery\UpdateIsDeleteForm;
use \Inc\DbQuery\UpdateIsDeleteQuestion;
use \Inc\DbQuery\UpdateIsDeleteOption;

 class TrashIsTrigger {

    private $updateIsDeletePost;
    private $updateIsDeleteParagraph;
    private $updateIsDeleteForm;
    private $updateIsDeleteQuestion;
    private $updateIsDeleteOption;


    public function updateDatabaseColumnIsDeleted( $post_id ) {
        //SELECT sefab_option.* FROM sefab_option, sefab_question, sefab_form, sefab_post
        //WHERE sefab_post.id = 2
        //AND sefab_form.post_id = sefab_post.id
        //AND sefab_question.form_id = sefab_form.id 
        //AND sefab_option.question_id = sefab_question.id;

        //Update post table
        $this->updateIsDeletePost->update( $post_id );
        //Update paragraph table
        $this->updateIsDeleteParagraph->update( $post_id );
        //Update form table
        //Update question table
        //Update option table
        
    }

    public function register() {
        $this->updateIsDeletePost      = new UpdateIsDeletePost;
        $this->updateIsDeleteParagraph = new UpdateIsDeleteParagraph;
        $this->updateIsDeleteForm      = new UpdateIsDeleteForm;
        $this->updateIsDeleteQuestion  = new UpdateIsDeleteQuestion;
        $this->updateIsDeleteOption    = new UpdateIsDeleteOption;

        add_action( 'trash_post', array($this, 'updateDatabaseColumnIsDeleted') );
    }

}

 
