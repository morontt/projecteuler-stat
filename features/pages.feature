Feature: pages
  Check simple pages

  Scenario: startpage
    Given I am on homepage
    Then I should see "Статистика по решениям задач"

    When I follow "Инфо"
    Then I should see "О проекте"
    And I should not see "Админка"