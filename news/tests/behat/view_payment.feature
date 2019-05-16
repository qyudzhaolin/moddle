@mod @mod_news
Feature: View a news
  In order to view a news
  As a user
  The type of the news affects how it is displayed.

  Scenario: View a news
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | manager1 | Manager | 1 | manager1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | manager1 | C1 | manager |
    #And the following "activities" exist:
      #| activity | name | description | course | idnumber |
     # | news | Test news | Test news description | C1 | news0 |
    And I log in as "manager1"
	 And I am on "Course 1" course homepage with editing mode on
	And I add a "news" to section "1" and I fill the form with:
      | name        | Test news name        |
      | description | Test news description |
	And I log out
	And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test news name"
	And I log out