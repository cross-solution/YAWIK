Feature: Dashboard
    In order to manage yawik
    As user
    I should able to access dashboard

    Scenario: Access homepage without login
        Given I am on the homepage
        Then I should see "Welcome to YAWIK!"
        And I should not see "Dashboard"

    Scenario: Successfully access homepage
        Given I am logged in as an administrator
        Then I should see "Welcome to YAWIK!"

    Scenario: Successfully access dashboard
        Given I am logged in as an administrator
        When I go to dashboard page
        Then the "h1" element should contain "Dashboard"
