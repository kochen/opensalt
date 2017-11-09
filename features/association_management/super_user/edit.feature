Feature: Editing a association management
  In order to confirm the application can edit association management
  As an super-user
  I need to see a all of the fields

  @incomplete @smoke @super-user @association @edit-association
  Scenario Outline: 1017-0933 An super-user can edit a association group
    Given I log in as a user with role "Super-User"
    Then I create a "Draft" framework
    And  I add "Group 1" Manange Assiation Groups
    Then I edit the "<title>"
    And  I edit the "<description">
    And I see the updated "<title>"
    And I see the updated "<description>"
    Then I delete "Draft" framework
    Examples:
      | title     | description     |
      | Group 1 new | New description |
