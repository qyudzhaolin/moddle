@mod @mod_news
Feature: Preview a news as a teacher
  In order to verify my quizzes are ready for my students
  As a teacher
  I need to be able to preview them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Teacher   | One      | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher  | C1     | teacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | news       | news 1 | news 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And news "news 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And I log in as "teacher"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Preview a news
    When I follow "news 1"
    And I press "Preview news now"
    Then I should see "25.00 out of 100.00"
    And I follow "Finish review"
    And "Review" "link" in the "Preview" "table_row" should be visible
