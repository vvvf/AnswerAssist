<?php
require_once(__dir__ . '/Question.php');
require_once(__dir__ . '/Answer.php');

$question = new Question();
$question->display();

$QuestionWord = $question->getQuestionWord();
$AnswerWords = $question->getAnswerWords();

$answer = new Answer($QuestionWord, $AnswerWords);
$answer->printAppearTime();
$answer->printBestAnswer();