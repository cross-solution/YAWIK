@install
Feature: Yawik installation
    In order to start using Yawik
    As an administrator
    I should able to install yawik application


    Background:
        Given I don't have "install@example.com" user
        And I have install module activated

    Scenario: Successfully install yawik
        Given I go to the install page
        Then I should see "Prerequisites"
        When I fill database connection with an active connection
        And I fill in "Initial user name" with "test"
        And I fill in "Password" with "test"
        And I fill in "Email address for system messages" with "install@example.com"
        And I press "Install"
        And I wait for the ajax response
        Then I should see "An administrator account with the login name \"test\" was created successfully"
        And I should see "The base configuration file was successfully created"
        And I should see "Start using YAWIK"

    Scenario: Install yawik with empty configuration
        Given I go to the install page
        And I press "Install"
        And I wait for the ajax response
        Then I should see "Value is required"

    Scenario: Install yawik with invalid database connection
        Given I go to the install page
        And I fill in "db_conn" with "invalid"
        And I press "Install"
        And I wait for the ajax response
        Then I should see "Invalid connection string"
