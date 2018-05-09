Feature: Creating a company
  As an unregistered user
  I should be able to create a company and login
  So that I can access the application

  Scenario: Create an account
    When I post a company to "/companies"
    And I debug
    Then I should receive a 201 response

  Scenario: Invalid email
    When I post a company to "/companies" with the following changes:
      | key                     | value   |
      | data.users.0.data.email | invalid |
    Then I should receive a 422 response
