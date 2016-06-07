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
    And I should see "Админка"

  Scenario: CRUD solutions
    Given I am logged as admin
    When I follow "Админка"
    And I follow "Решения"
    Then I should see "Решения" in the ".page-header" element

    When I follow "Создать"
    And I fill in the following:
      | Problem number | 11     |
      | Execution time | 12.030 |
      | Deviation time | 0.005  |
    And I press "Submit"
    Then I should see "Решение создано"
    And I should see "12.03"

    When I follow "Редактировать"
    And I fill in the following:
      | Execution time | 12.007 |
      | Deviation time | 0.013  |
    And I press "Submit"
    Then I should see edit solutions flash
    Then I should see "12.007"

    When I press "Удалить"
    Then I should see delete solutions flash
    And I should not see "12.007"

  Scenario: CRUD languages
    Given I am logged as admin
    When I follow "Админка"
    And I follow "ЯП"
    Then I should see "Языки программирования" in the ".page-header" element

    When I follow "Создать"
    And I fill in the following:
      | Name    | PHP |
      | Comment | 5.5 |
    And I press "Submit"
    Then I should see "Язык PHP (5.5) создан"
    And I should see "5.5" in the ".table" element

    When I follow "Редактировать"
    And I fill in the following:
      | Comment | 7.0 |
    And I press "Submit"
    Then I should see "Язык PHP (7.0) отредактирован"
    Then I should see "7.0" in the ".table" element

    When I press "Удалить"
    Then I should see "Язык PHP (7.0) удалён"
    And I should not see "7.0" in the ".table" element
