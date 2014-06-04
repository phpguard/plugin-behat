Feature: Developer change some file
    As a Developer
    I want to run behat command
    In order to get feedback on a state of my application

    Background:
        Given the config file contains:
              """
              behat:
                  options:
                      all_after_pass: true
                  watch:
                      - { pattern: "#^features/(.+)\.feature#" }
              """
          And the feature file "features/success.feature" contains:
              """
              Feature: Success Feature
                As something
                I want something
                In order to get something

                Scenario: Some Success Scenario
                    Given I have passed step
                     Then I have passed step
              """

    Scenario: Running successfully behat with phpguard patch
        Given I start phpguard
          And I create file "features/some.feature" with:
              """
              Feature: Some Feature
                As something
                I want something
                In order to get something

                Scenario: Some Scenario
                    Given I have passed step
                     Then I have failed step
              """
         Then I should see "1 scenario"
          And I should see "2 steps"
          And I should see "1 passed"
          And I should see "1 failed"
          And I should see "[FAIL]*Feature: Some Feature"
          And I should see "[FAIL]*Scenario: Some Scenario"
          And I should see "[FAIL]*Then I have failed step"

    Scenario: Should keep failed test to run
        Given the feature file "features/some.feature" contains:
              """
              Feature: Some Feature
                As something
                I want something
                In order to get something

                Scenario: Some Scenario
                    Given I have passed step
                     Then I have failed step
              """
          And I start phpguard
         When I run phpguard with "all -vvv"
         Then I should see "2 scenario"
          And I should see "4 steps"
          And I should see "3 passed, 1 failed"

         When I run phpguard with "all -vvv"
         Then I should see "1 scenario"
          And I should see "2 steps"
          And I should see "1 passed, 1 failed"

         When I modify file "features/some.feature" with:
              """
              Feature: Some Feature
                As something
                I want something
                In order to get something


                Scenario: Some Scenario
                    Given I have passed step
                     Then I have passed step
              """
         Then I should see "2 passed"
          And I should see "2 scenario"