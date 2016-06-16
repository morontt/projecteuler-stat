Feature: pages
  Check simple pages

  Scenario: startpage
    Given I am on homepage
    Then I should see "Статистика по решениям задач"

    When I follow "Инфо"
    Then I should see "О проекте"
    And I should not see "Админка"

  Scenario: user page
    Given I am on "/user/pacocha-ludwig"
    Then I should see "Результаты пользователя pacocha.ludwig"

  Scenario: problem page
    Given I am on "/problem/10"
    Then I should see "Summation of primes"

  Scenario: about page
    Given I am on "/about"
    Then I should see "Изначально планировал"
