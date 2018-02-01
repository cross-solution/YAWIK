Feature: Organization profile
    In order to apply a job
    As an applicant
    I should able to view organization profile

    Background:
        Given I have organization "Cross Solution" with published jobs:
            | title             | professions | industries      | employment types |
            | Software Engineer | IT          | IT & Internet   | Contract         |
            | Tester            | IT          | IT & Internet   | Permanent        |
            | Senior Developer  | IT          | IT & Internet   | Permanent        |

    @organization @profile
    Scenario: Browse Profile Page For an Organization
        Given I go to profile page for organization "Cross Solution"
        Then I should see "Cross Solution"
        And I should see "Software Engineer"
        And I should see "Tester"
        And I should see "Senior Developer"
