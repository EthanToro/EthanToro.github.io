<?php
class Comment_Handler {
    private
        $comment,
        $chain;

    public function __construct(&$comment) {
        $this->comment = $comment;
        $this->chain[]= new inSettings();
        $this->chain[]= new isSpam();
        $this->chain[]= new checkPerson();
    }
    public function process() {
        foreach($this->chain as $c){
            if(!$c->process($this->comment)){
                return false;
            }
        }
        return true;
    }
}

abstract class Comment_Handle {
    abstract function process(&$comment); //return true and false
}

class isSpam extends Comment_Handle {
    public function process(&$comment) {
        $content = $comment->getContent();        
        $rating = $comment->getRating();

        $badWords = array(
            'finkleBerry',
            'darn',
            'buttonPolisher',
            'chimney sweet',
            'pig',
            'buy',
            'purchase',
            'drugs',
            'religion',
            'obama',
            'shucks',   // Oh, so crude     
            '<a>', // you should see what wordpresses looks like
            'href',
            '</a>'
        );

        $penalty = 5;

        foreach ($badWords as $bw)
            if (stripos($content, $bw)) {
                $rating -= $penalty;
                $penalty += 5;
            }

        $comment->edit(array('rating' => $rating));
        if ($comment->getRating() > 0){
            return true;
        }else{
            return false;
        }
    }
}

class checkPerson extends Comment_Handle {
    public function process(&$comment) {
        $rating = $comment->getRating();
        if (!$comment->isAnon()) {
            $user = $comment->getUser();

            $limitTime = time() - 6040800;
            if ($limitTime < $user->getJoinDate())
                $rating -= 8;

            if ($user->getCommentCount() < 5)
                $rating -= 6;

        } else {
            $rating -= 12;
        }


        $comment->edit(array('rating' => $rating));
        if ($comment->getRating() > 0){
            return true;
        }else{
            return false;
        }
    }
}

class inSettings extends Comment_Handle {
    public function process(&$comment) {
        if ($comment->getRating() > 0){
            return true;
        }else{
            return false;
        }
    }
}

?>
