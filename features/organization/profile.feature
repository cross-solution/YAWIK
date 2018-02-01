Feature: Organization profile
    In order to apply a job
    As an applicant
    I should able to view organization profile

    Background:
        Given I have organization "Cross Solution"

    @organization @profile
    Scenario: Browse Profile Page For an Organization
        Given I go to profile page for organization "Cross Solution"
