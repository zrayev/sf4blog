Feature: Sign in to the website
  Scenario: Log in with email and password
    Given I am on "/login"
    When I fill in the following:
      | email | admin@example.com |
      | password | test |
    And I press "Sign in"
    Then I should be on "/"
    And I should see "Logout"

  Scenario: Log in with bad credentials
    Given I am on "/login"
    When I fill in the following:
      | email | bla@example.com |
      | password | bla         |
    And I press "Sign in"
    Then I should be on "/login"
    And I should see "Email could not be found."
