@mod @mod_news
Feature: Add a news
  In order to setting activity charge fee  
  As a manager
  I need to create a news

  Scenario:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Terry1    | Manager1 | Manager1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | manager1 | C1 | manager |
    When I log in as "manager1"
    And I am on "Course 1" course homepage with editing mode on
	And I add a "news" to section "1" and I fill the form with:
      | name        | Test news name        |
      | description | Test news description |
    And I log out
