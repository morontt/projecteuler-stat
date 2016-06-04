Feature: backend
  Check backend pages

  Scenario: dashboard
    Given I am on "/area_51/"
    Then I should be on "/login"

    When I fill in the following:
      | Username | admin |
      | Password | test  |
    And I press "Логин"
    Then I should see "Панель управления"
