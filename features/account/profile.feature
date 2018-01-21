Feature: Editing user profile
    In order to manage my personal information
    As a User
    I want to be able to edit my personal information

    Background:
        Given there is a user with the following:
            | email         | test@profile.com |
            | login         | test.profile     |
            | password      | test             |
            | fullname      | Test Profile     |
        And I log in with username "test@profile.com" and password "test"

    Scenario: Successfully access profile page
        When I go to profile page
        Then I should see "My profile"
        And I should see "Personal Informations"
        And I should see "Test Profile"

    Scenario: Edit profile page
        When I go to profile page
        And I hover over the element ".sf-container"
        And I wait for the ajax response
        And I press "Edit"
        And I wait for the ajax response
        Then I should see "Salutation"
        When I fill in the following:
            | info-firstName    | Test                       |
            | info-lastName     | Profile Edited             |
            | info-street       | Some Street Address        |
            | info-houseNumber  | 77777                      |
            | info-postalCode   | 12345                      |
            | info-city         | New York                   |
            | info-phone        | 6212345                    |
            | info-email        | test-edited@profile.com    |
        And I select "Mr." from "info-gender"
        And I press "Save"
        And I wait for the ajax response
        And I wait for 3 seconds
        Then I should see "Test Profile Edited"
        And I should see "Some Street Address"
        And I should see "77777"
        And I should see "12345"
        And I should see "New York"
        And I should see "6212345"
        And I should see "test-edited@profile.com"
        And I should see "Mr."

    Scenario: Update profile photo
        When I go to profile page
        And I attach the file "img/person.jpg" to "info-image-image"
        And I wait for the ajax response
        Then I should see an "img.img-polaroid" element
