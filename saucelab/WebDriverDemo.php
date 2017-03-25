<?php

require_once 'coreDemo.php';

class WebDriverDemo extends CoreDemo
{

   // protected $start_url = 'http://saucelabs.com/test/guinea-pig';

    
    public function testTitle1()
    {
        $this->assertContains("I am a page title", $this->title());
    }

    public function testTitle()
    {
        $this->assertContains("I am a page title", $this->title());
    }

    public function testLink()
    {
        $link = $this->byId('i am a link');
        $link->click();
        $this->assertContains("I am another page title", $this->title());
    }

    public function testTextbox()
    {
        $test_text = "This is some text";
        $textbox = $this->byId('i_am_a_textbox');
        $textbox->click();
        $this->keys($test_text);
        $this->assertEquals($textbox->value(), $test_text);
    }

    public function testSubmitComments()
    {
        $comment = "This is a very insightful comment.";
        $this->byId('comments')->value($comment);
        $this->byId('submit')->submit();
        $driver = $this;

        $comment_test = function() use ($comment, $driver) {
            $text = $driver->byId('your_comments')->text();
            return $text == "Your comments: $comment";
        };

        $this->spinAssert("Comment never showed up!", $comment_test);

    }

}
