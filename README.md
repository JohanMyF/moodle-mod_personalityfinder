# PersonalityFinder

**PersonalityFinder** is a Moodle activity plugin for building user-defined personality reflection instruments.

The plugin is intended for teachers, pastors, counsellors, volunteer placement advisors, recruitment officers, facilitators, and other authorised users who want respondents to reflect on personal attributes, focused personality dimensions, and broader general dimensions.

PersonalityFinder does **not** hard-code a particular personality model. It does not assume that every activity must use fixed dimensions such as Task-oriented vs People-oriented or Structured vs Unstructured. Instead, it provides a flexible framework where the activity creator defines the attributes, dimensions, rating scales, labels, descriptions, scoring, interpretation text, and visual outputs.

The plugin may be used in pastoral, educational, developmental, vocational, volunteer-placement, recruitment, and self-reflection settings.

PersonalityFinder is especially suitable for contexts where personality is explored as part of a wider reflection process, for example the **P** component of the S.H.A.P.E. framework:

- **S**piritual Gifts
- **H**eart / Passion
- **A**bilities / Aptitudes
- **P**ersonality
- **E**xperience

However, the plugin should not be limited to S.H.A.P.E. use. The intention is to create a general-purpose reflection tool that can support many different locally designed instruments.

---

## 1. Purpose

PersonalityFinder helps respondents reflect on how they may be perceived, how they tend to choose between contrasting preferences, and how they locate themselves on user-defined personality or behavioural dimensions.

The plugin supports three broad sections:

1. **Personality Attributes**
2. **Focus Dimensions**
3. **General Dimensions**

Each section is fully user-defined by the activity creator.

The teacher, pastor, counsellor, advisor, or investigator decides:

- Which attributes are shown
- Which two dimensions are treated as the main focus dimensions
- Which semantic differential items explore those focus dimensions
- Which broader general dimensions are included
- Which scale lengths are used
- Which labels appear at the left and right poles of each scale
- How results are interpreted
- What visual output is shown
- What text appears in the respondent’s results
- What presets can be imported or exported through JSON

---

## 2. Important Disclaimer

PersonalityFinder is a **reflection tool**, not a formal psychometric test.

It is not intended to diagnose personality, assess mental health, provide clinical interpretation, or replace professional psychometric assessment.

Unless the activity creator has independently validated a specific instrument and has the authority to use it, PersonalityFinder should not be used for high-stakes decisions such as:

- Employment selection
- Ordination
- Clinical diagnosis
- Psychological screening
- Formal counselling assessment
- Promotion or disciplinary decisions
- Exclusion from ministry or service opportunities

Recommended user-facing wording:

> This activity is designed to help you reflect on your personality preferences and how others may experience you. It is not a clinical, diagnostic, or formal psychometric assessment. Your results should be used as a starting point for reflection and conversation, not as a fixed label.

---

## 3. Design Philosophy

PersonalityFinder is built around flexibility and interpretive humility.

The plugin should not impose one personality theory. Instead, it should allow the activity creator to build instruments around locally appropriate constructs.

Examples of possible focus dimensions include:

- Task-oriented ↔ People-oriented
- Structured ↔ Unstructured
- Direct ↔ Diplomatic
- Reserved ↔ Expressive
- Analytical ↔ Relational
- Planned ↔ Spontaneous
- Detail-focused ↔ Big-picture
- Independent ↔ Collaborative
- Cautious ↔ Bold
- Reflective ↔ Action-oriented

These examples are not hard-coded requirements. They are only possible uses of the framework.

PersonalityFinder should avoid rigid typing language. Preferred result language includes:

- “You may tend toward...”
- “Your responses suggest...”
- “You may feel more comfortable when...”
- “You may wish to reflect on...”
- “Others may experience you as...”
- “This may be a useful area for conversation...”

The plugin should avoid over-certain language such as:

- “You are definitely...”
- “Your personality type is...”
- “You should only serve in...”
- “You are unsuitable for...”
- “This proves that...”

---

## 4. The Three Sections

PersonalityFinder activities are built from three configurable sections.

---

# Section 1: Personality Attributes

## 4.1 Purpose

The Personality Attributes section presents a user-defined list of descriptive attributes.

The respondent selects attributes that may describe how others experience them.

Depending on the activity settings, the section may also allow respondents to select attributes that they use to describe themselves.

The activity creator defines the attribute list.

Examples:

- Courageous
- Steadfast
- Ordered
- Big-picture
- Talkative
- Gentle
- Analytical
- Encouraging
- Practical
- Creative

These examples are optional. Every item should be editable.

---

## 4.2 Possible Attribute Selection Modes

The plugin should support configurable selection modes.

### Mode A: Others Describe Me

The respondent sees a list of attributes and selects the ones they believe others may use to describe them.

Prompt example:

> Select the words that others may use to describe how you usually come across.

This mode is simple and works well when the purpose is to reflect on visible behaviour and social perception.

---

### Mode B: I Describe Myself

The respondent sees a list of attributes and selects the ones they believe describe themselves.

Prompt example:

> Select the words that you would use to describe yourself.

This mode focuses on self-perception.

---

### Mode C: Self and Others in One Pass

Each attribute appears once, with two checkboxes:

- I describe myself this way
- Others may describe me this way

Example:

| Attribute | I describe myself this way | Others may describe me this way |
|---|---:|---:|
| Courageous | ☐ | ☐ |
| Steadfast | ☐ | ☐ |
| Ordered | ☐ | ☐ |
| Big-picture | ☐ | ☐ |
| Talkative | ☐ | ☐ |

This mode allows comparison between self-perception and perceived external perception without repeating the attribute list.

---

### Mode D: Two-Pass Reflection

The same attribute list is shown twice.

First pass:

> I describe myself as...

Second pass:

> Others may describe me as...

This mode encourages deeper reflection, but it takes longer to complete.

---

## 4.3 Recommended Default Attribute Mode

The recommended default is:

> **Self and Others in One Pass**

This is efficient, easy to understand, and still allows meaningful comparison.

However, the activity creator should be able to choose the mode.

---

## 4.4 Attribute Interpretation

The attribute section should not produce a formal personality score unless the activity creator explicitly designs such scoring.

By default, it should produce a reflective summary:

- Attributes selected as self-descriptions
- Attributes selected as perceived descriptions by others
- Attributes selected in both columns
- Attributes selected only in one column

Possible reflection prompts:

- Which selected attributes feel most accurate?
- Which selected attributes have others affirmed in you?
- Which selected attributes surprised you?
- Are there qualities others may see in you that you tend to overlook?
- Are there qualities you value in yourself that others may not yet see?
- Which qualities could become weaknesses if overused?

---

## 4.5 Attribute Data Requirements

Each attribute should support:

- Unique ID
- Label
- Optional description
- Optional category
- Optional display order
- Optional enabled/disabled status

Example:

```json
{
  "id": "steadfast",
  "label": "Steadfast",
  "description": "Reliable, loyal, and consistent over time.",
  "category": "Reliability",
  "sortorder": 10,
  "enabled": true
}
```

---

# Section 2: Focus Dimensions

## 5.1 Purpose

The Focus Dimensions section is the core analytical section of PersonalityFinder.

The activity creator defines **two main dimensions** that the instrument will focus on.

In one example, the two focus dimensions may be:

1. Task-oriented ↔ People-oriented
2. Structured ↔ Unstructured

However, these are only examples. The plugin should allow the activity creator to define any two focus dimensions.

Examples of possible focus dimensions:

- Reserved ↔ Expressive
- Analytical ↔ Relational
- Planned ↔ Spontaneous
- Independent ↔ Collaborative
- Detail-focused ↔ Big-picture
- Cautious ↔ Bold
- Direct ↔ Diplomatic
- Reflective ↔ Action-oriented
- Consistent ↔ Adaptive
- Practical ↔ Conceptual

The two focus dimensions are explored through user-defined semantic differential items.

---

## 5.2 What Is a Focus Dimension?

A focus dimension is a bipolar construct with two labelled poles.

Example:

```text
Task-oriented ↔ People-oriented
```

Each focus dimension should include:

- Dimension ID
- Left pole label
- Right pole label
- Optional short name
- Optional description
- Scale length for its items
- Any number of semantic differential items
- Scoring direction
- Result interpretation text
- Optional visual label text
- Optional quadrant label logic if paired with the second focus dimension

---

## 5.3 Semantic Differential Items

A semantic differential item presents two contrasting statements with a rating scale between them.

Example:

```text
I notice first what needs to be done.  1 - 2 - 3 - 4  I notice first how people are feeling.
```

The respondent selects the point that best reflects their preference.

The activity creator defines:

- Left statement
- Right statement
- Number of scale points
- Whether the left side represents the left pole of the dimension
- Whether the item should be reverse-scored
- Optional item help text
- Optional item display order

---

## 5.4 Number of Items Per Focus Dimension

The plugin should allow any number of items per focus dimension.

Recommended minimum:

- 3 items per focus dimension

Recommended default:

- 6 items per focus dimension

Possible advanced setting:

- Warn the activity creator if a focus dimension has fewer than 3 items.

The plugin should not require exactly six items. Six is only a useful default.

---

## 5.5 Scale Length for Focus Dimension Items

The activity creator should be able to define the scale length for each focus dimension or for the entire activity.

Common options:

- 4-point scale
- 5-point scale
- 6-point scale
- 7-point scale
- 8-point scale
- Custom scale length

The earlier example used a 4-point scale:

```text
Left statement  1 - 2 - 3 - 4  Right statement
```

A 4-point scale is useful when the creator wants respondents to choose a leaning and avoid a neutral midpoint.

However, the plugin should not force a 4-point scale.

Recommended setting:

```json
{
  "focus_scale_points": 4,
  "allow_neutral": false
}
```

If the activity creator chooses an odd number of scale points, a neutral midpoint becomes possible.

---

## 5.6 Example Focus Dimension Configuration

Example only:

```json
{
  "focus_dimensions": [
    {
      "id": "task_people",
      "left_label": "Task-oriented",
      "right_label": "People-oriented",
      "description": "Reflects whether the respondent tends to focus first on task completion or relational experience.",
      "scale_points": 4,
      "items": [
        {
          "id": "tp1",
          "left_statement": "I notice first what needs to be done.",
          "right_statement": "I notice first how people are feeling.",
          "reverse_scored": false,
          "sortorder": 10
        },
        {
          "id": "tp2",
          "left_statement": "I feel satisfied when a task is completed well.",
          "right_statement": "I feel satisfied when people feel included and encouraged.",
          "reverse_scored": false,
          "sortorder": 20
        }
      ]
    },
    {
      "id": "structured_unstructured",
      "left_label": "Structured",
      "right_label": "Unstructured",
      "description": "Reflects whether the respondent tends to prefer clear structure or flexible response.",
      "scale_points": 4,
      "items": [
        {
          "id": "su1",
          "left_statement": "I prefer to plan before I begin.",
          "right_statement": "I prefer to begin and adjust as I go.",
          "reverse_scored": false,
          "sortorder": 10
        },
        {
          "id": "su2",
          "left_statement": "I feel more comfortable when expectations are clear.",
          "right_statement": "I feel more comfortable when there is room to improvise.",
          "reverse_scored": false,
          "sortorder": 20
        }
      ]
    }
  ]
}
```

---

## 5.7 Focus Dimension Scoring

For each focus dimension, the plugin calculates:

- Raw item responses
- Reverse-scored item responses, where applicable
- Mean score
- Percentage position across the scale
- Left/right leaning
- Strength of leaning
- Optional interpretation band

Example for a 4-point scale:

- 1.00–1.75 = Strong left pole leaning
- 1.76–2.49 = Moderate left pole leaning
- 2.50–3.24 = Moderate right pole leaning
- 3.25–4.00 = Strong right pole leaning

However, because scale length is user-defined, interpretation bands should be calculated dynamically or defined by the activity creator.

---

## 5.8 Focus Dimension Results

Each focus dimension should display:

- Left pole label
- Right pole label
- Respondent’s score
- Visual marker on the scale
- Interpretation text
- Optional reflection prompt

Example:

```text
Task-oriented  [marker]  People-oriented
Your responses suggest a moderate leaning toward Task-oriented.
```

The wording should use the actual labels defined by the activity creator.

---

# Section 3: General Dimensions

## 6.1 Purpose

The General Dimensions section allows the activity creator to define any number of additional bipolar dimensions.

These dimensions may explore broader personality preferences, temperament descriptors, work style preferences, communication preferences, ministry preferences, or any other reflective constructs.

Examples:

- Introvert ↔ Extrovert
- Planned ↔ Spontaneous
- Intuitive ↔ Observant
- Thinking ↔ Feeling
- Direct ↔ Diplomatic
- Big-picture ↔ Detail-focused
- Independent ↔ Collaborative
- Cautious ↔ Bold
- Reserved ↔ Expressive
- Practical ↔ Conceptual

These examples are not fixed. The activity creator defines the dimensions.

---

## 6.2 General Dimension Format

Each general dimension is normally displayed as a semantic differential scale.

Example:

```text
Introvert  1 - 2 - 3 - 4 - 5 - 6 - 7 - 8  Extrovert
```

The activity creator defines:

- Dimension ID
- Left pole label
- Right pole label
- Scale length
- Optional description
- Optional prompt
- Optional left pole interpretation
- Optional right pole interpretation
- Optional reflection prompt
- Optional display order

---

## 6.3 Scale Length for General Dimensions

The activity creator should be able to define the number of scale points.

Examples:

- 4-point scale
- 5-point scale
- 6-point scale
- 7-point scale
- 8-point scale
- 10-point scale
- Custom scale length

The previous example used an 8-point scale:

```text
Introvert  1 - 2 - 3 - 4 - 5 - 6 - 7 - 8  Extrovert
```

An 8-point scale gives more nuance and avoids a single neutral midpoint.

The plugin should support consistent display of all selected scale lengths.

---

## 6.4 Including the Two Focus Dimensions in the General Dimension Section

The two focus dimensions explored in Section 2 should be displayed again in the General Dimensions section.

This creates a unified personality profile where all dimensions are shown on the same kind of scale.

For example, if the General Dimensions section uses an 8-point scale, and the Focus Dimensions used 4-point item averages, the plugin should convert the calculated focus dimension results to the 8-point general scale.

Example:

Section 2 calculated:

```text
Task-oriented ↔ People-oriented average: 1.83 on a 4-point scale
Structured ↔ Unstructured average: 2.12 on a 4-point scale
```

Section 3 displays:

```text
Task-oriented  1 - 2 - 3 - 4 - 5 - 6 - 7 - 8  People-oriented
Structured     1 - 2 - 3 - 4 - 5 - 6 - 7 - 8  Unstructured
```

The values are scaled to match the General Dimension scale.

---

## 6.5 Pre-Populated Focus Dimensions

The two focus dimensions should be pre-populated in the General Dimensions section based on Section 2 results.

The activity creator should be able to choose whether respondents may adjust these values.

Recommended setting:

```json
{
  "prepopulate_focus_dimensions_in_general_section": true,
  "allow_respondent_adjustment": true,
  "store_calculated_and_adjusted_values": true
}
```

Recommended prompt:

> Based on your earlier responses, the system has estimated your position on the two focus dimensions. You may accept these positions or adjust them if another position feels more accurate after reflection.

This keeps the activity reflective rather than mechanical.

---

## 6.6 Scaling Formula

To convert a score from one scale to another:

```text
converted_score = round(((raw_score - source_min) / (source_max - source_min)) * (target_max - target_min) + target_min)
```

Where:

- `raw_score` is the calculated score from Section 2
- `source_min` is normally 1
- `source_max` is the number of points in the focus item scale
- `target_min` is normally 1
- `target_max` is the number of points in the general dimension scale

Example:

Converting a 4-point average to an 8-point scale:

```text
converted_score = round(((average - 1) / 3) * 7 + 1)
```

---

## 6.7 General Dimension Results

The result page should show all general dimensions, including the two focus dimensions.

Each dimension should show:

- Left pole label
- Right pole label
- Respondent’s selected or calculated position
- Optional interpretation
- Optional reflection prompt

The plugin should clearly indicate whether the value was:

- Directly selected by the respondent
- Calculated from focus dimension items
- Calculated first and then adjusted by the respondent

---

# Visual Output

## 7.1 Main Visual Matrix

When two focus dimensions are defined, PersonalityFinder can display a 2 x 2 matrix based on those dimensions.

The matrix should not assume fixed labels.

The four quadrants should be defined by the activity creator.

Example only:

|  | Structured | Unstructured |
|---|---|---|
| **Task-oriented** | Organiser / Implementer | Starter / Problem-solver |
| **People-oriented** | Shepherd / Facilitator | Connector / Encourager |

In another instrument, the same visual structure might become:

|  | Direct | Diplomatic |
|---|---|---|
| **Analytical** | Critical Evaluator | Careful Advisor |
| **Relational** | Active Advocate | Bridge Builder |

The plugin should allow all quadrant labels, summaries, descriptions, strengths, risks, and prompts to be user-defined.

---

## 7.2 Matrix Axis Configuration

The matrix uses the two focus dimensions.

The activity creator should be able to define:

- Which focus dimension appears on the horizontal axis
- Which focus dimension appears on the vertical axis
- Whether lower values appear left/top or right/bottom
- Axis labels
- Quadrant labels
- Quadrant descriptions
- Centre-zone interpretation
- Borderline interpretation
- Result marker style

Recommended defaults:

- Focus Dimension 1 = vertical axis
- Focus Dimension 2 = horizontal axis
- Left pole of horizontal dimension = left side
- Right pole of horizontal dimension = right side
- Left pole of vertical dimension = top
- Right pole of vertical dimension = bottom

However, these should be configurable.

---

## 7.3 Matrix Result Marker

The respondent’s result should be shown as a marker on the 2 x 2 matrix.

The marker should represent the respondent’s calculated position on the two focus dimensions.

The matrix should support:

- Clear quadrant labels
- Respondent marker
- Accessible text result
- Mobile-friendly display
- Non-colour visual cues
- Optional centre-zone result

The result should always be available as text as well as graphics.

---

## 7.4 Centre and Borderline Results

The plugin should handle respondents whose scores fall near the centre or near quadrant borders.

The activity creator should be able to configure:

- Whether centre-zone results are allowed
- How wide the centre zone is
- What centre-zone interpretation text is displayed
- Whether borderline results show one quadrant or two possible adjacent quadrants

Example centre-zone message:

> Your responses are close to the centre of the matrix. This may mean that you are adaptable across different settings, or that your preferences depend strongly on the situation. Rather than forcing yourself into one quadrant, reflect on which situations energise you most and which situations drain you most.

---

# JSON Import and Export

## 8.1 Purpose

PersonalityFinder should support JSON import and export so that teachers, pastors, counsellors, advisors, and other activity creators can create, share, adapt, and reuse instruments.

This is a key feature of the plugin.

A user should be able to:

- Export a complete instrument as JSON
- Import a complete instrument from JSON
- Share JSON presets with other users
- Adapt an imported instrument
- Build local libraries of instruments
- Reuse instruments across Moodle courses and sites

---

## 8.2 What JSON Should Include

A complete JSON preset should include:

- Instrument title
- Introduction text
- Disclaimer text
- Attribute list
- Attribute selection mode
- Focus dimension definitions
- Focus dimension semantic differential items
- Focus dimension scale settings
- General dimension definitions
- General dimension scale settings
- Matrix configuration
- Quadrant labels
- Quadrant descriptions
- Result interpretation text
- Reflection prompts
- PDF settings
- Display settings
- Version metadata

---

## 8.3 Example Full JSON Skeleton

```json
{
  "schema": "mod_personalityfinder",
  "schema_version": "1.0",
  "instrument": {
    "title": "Personality Reflection for Ministry Fit",
    "description": "A reflective activity to help respondents consider how their personality may shape the way they serve.",
    "disclaimer": "This activity is intended for reflection and conversation. It is not a clinical, diagnostic, or formal psychometric assessment."
  },
  "settings": {
    "attribute_mode": "self_and_others_one_pass",
    "show_matrix": true,
    "allow_focus_adjustment_in_general_dimensions": true,
    "store_calculated_and_adjusted_values": true,
    "enable_pdf": true
  },
  "attributes": [],
  "focus_dimensions": [],
  "general_dimensions": [],
  "matrix": {},
  "result_text": {},
  "reflection_prompts": []
}
```

---

## 8.4 Example JSON With User-Defined Dimensions

```json
{
  "schema": "mod_personalityfinder",
  "schema_version": "1.0",
  "instrument": {
    "title": "Example Personality Reflection",
    "description": "An example instrument using Task vs People and Structured vs Unstructured as focus dimensions.",
    "disclaimer": "This is a reflection tool, not a formal psychometric assessment."
  },
  "settings": {
    "attribute_mode": "self_and_others_one_pass",
    "show_matrix": true,
    "allow_focus_adjustment_in_general_dimensions": true,
    "store_calculated_and_adjusted_values": true,
    "general_dimension_scale_points": 8
  },
  "attributes": [
    {
      "id": "courageous",
      "label": "Courageous",
      "description": "Willing to act with bravery when something important is at stake.",
      "sortorder": 10,
      "enabled": true
    },
    {
      "id": "steadfast",
      "label": "Steadfast",
      "description": "Reliable, loyal, and consistent over time.",
      "sortorder": 20,
      "enabled": true
    }
  ],
  "focus_dimensions": [
    {
      "id": "task_people",
      "left_label": "Task-oriented",
      "right_label": "People-oriented",
      "description": "Reflects whether the respondent tends to focus first on completion or relational experience.",
      "scale_points": 4,
      "items": [
        {
          "id": "tp1",
          "left_statement": "I notice first what needs to be done.",
          "right_statement": "I notice first how people are feeling.",
          "reverse_scored": false,
          "sortorder": 10
        },
        {
          "id": "tp2",
          "left_statement": "I feel satisfied when a task is completed well.",
          "right_statement": "I feel satisfied when people feel included and encouraged.",
          "reverse_scored": false,
          "sortorder": 20
        }
      ]
    },
    {
      "id": "structured_unstructured",
      "left_label": "Structured",
      "right_label": "Unstructured",
      "description": "Reflects whether the respondent tends to prefer clear structure or flexible response.",
      "scale_points": 4,
      "items": [
        {
          "id": "su1",
          "left_statement": "I prefer to plan before I begin.",
          "right_statement": "I prefer to begin and adjust as I go.",
          "reverse_scored": false,
          "sortorder": 10
        },
        {
          "id": "su2",
          "left_statement": "I feel more comfortable when expectations are clear.",
          "right_statement": "I feel more comfortable when there is room to improvise.",
          "reverse_scored": false,
          "sortorder": 20
        }
      ]
    }
  ],
  "general_dimensions": [
    {
      "id": "introvert_extrovert",
      "left_label": "Introvert",
      "right_label": "Extrovert",
      "scale_points": 8,
      "description": "Reflects where the respondent tends to draw energy.",
      "sortorder": 10
    },
    {
      "id": "planned_spontaneous",
      "left_label": "Planned / Controlled",
      "right_label": "Spontaneous / Flexible",
      "scale_points": 8,
      "description": "Reflects how the respondent relates to planning, order, and flexibility.",
      "sortorder": 20
    },
    {
      "id": "task_people",
      "source": "focus_dimension",
      "source_dimension_id": "task_people",
      "left_label": "Task-oriented",
      "right_label": "People-oriented",
      "scale_points": 8,
      "description": "Pre-populated from the earlier Task vs People focus dimension.",
      "allow_adjustment": true,
      "sortorder": 30
    },
    {
      "id": "structured_unstructured",
      "source": "focus_dimension",
      "source_dimension_id": "structured_unstructured",
      "left_label": "Structured",
      "right_label": "Unstructured",
      "scale_points": 8,
      "description": "Pre-populated from the earlier Structured vs Unstructured focus dimension.",
      "allow_adjustment": true,
      "sortorder": 40
    }
  ],
  "matrix": {
    "enabled": true,
    "vertical_dimension_id": "task_people",
    "horizontal_dimension_id": "structured_unstructured",
    "centre_zone": 0.15,
    "quadrants": [
      {
        "id": "organiser_implementer",
        "vertical_pole": "left",
        "horizontal_pole": "left",
        "label": "Organiser / Implementer",
        "summary": "You may naturally bring order, clarity, responsibility, and follow-through.",
        "prompt": "How can you use your love of order and completion to serve people well?"
      },
      {
        "id": "starter_problem_solver",
        "vertical_pole": "left",
        "horizontal_pole": "right",
        "label": "Starter / Problem-solver",
        "summary": "You may naturally bring energy, initiative, adaptability, and practical problem-solving.",
        "prompt": "How can you turn your energy for action into something that others can understand, join, and continue?"
      },
      {
        "id": "shepherd_facilitator",
        "vertical_pole": "right",
        "horizontal_pole": "left",
        "label": "Shepherd / Facilitator",
        "summary": "You may naturally bring care, consistency, relational responsibility, and thoughtful facilitation.",
        "prompt": "How can you care for people faithfully while also keeping healthy boundaries and helping the group move forward?"
      },
      {
        "id": "connector_encourager",
        "vertical_pole": "right",
        "horizontal_pole": "right",
        "label": "Connector / Encourager",
        "summary": "You may naturally bring warmth, flexibility, encouragement, and relational energy.",
        "prompt": "How can you use your relational warmth in ways that are sustainable, dependable, and connected to the wider work?"
      }
    ]
  },
  "reflection_prompts": [
    "Which part of your result feels most accurate?",
    "Which part surprised you?",
    "Where have others seen these qualities in you?",
    "Which settings tend to energise you?",
    "Which settings tend to drain you?",
    "What kind of team helps you flourish?"
  ]
}
```

---

# Teacher / Investigator Workflow

## 9.1 Creating an Activity

The activity creator should be able to create a new PersonalityFinder activity by:

1. Adding the activity to a Moodle course
2. Naming the instrument
3. Writing introductory text
4. Choosing or importing a JSON preset
5. Defining the Personality Attributes section
6. Defining the two Focus Dimensions
7. Adding semantic differential items for each Focus Dimension
8. Defining the General Dimensions
9. Configuring the visual matrix
10. Editing result text and reflection prompts
11. Previewing the respondent experience
12. Saving the activity

---

## 9.2 Editing Instruments

The plugin should provide an instrument builder interface where the activity creator can:

- Add attributes
- Edit attributes
- Delete attributes
- Reorder attributes
- Add focus dimensions
- Edit focus dimension labels
- Add semantic differential items
- Edit item wording
- Reverse-score items
- Set scale lengths
- Add general dimensions
- Reorder general dimensions
- Configure whether focus dimensions appear in the general section
- Edit matrix labels
- Edit quadrant descriptions
- Import JSON
- Export JSON
- Validate JSON before saving

---

## 9.3 JSON Validation

When importing JSON, the plugin should validate:

- Correct schema name
- Supported schema version
- Required fields
- Unique IDs
- Exactly two focus dimensions
- At least one item per focus dimension
- Valid scale point values
- Matrix references to existing focus dimensions
- General dimension references to existing focus dimensions
- Valid quadrant definitions
- No duplicate sort orders where uniqueness is required
- Safe text handling according to Moodle standards

Invalid JSON should not break the activity. The plugin should show helpful error messages.

---

# Respondent Workflow

## 10.1 Completing the Activity

The respondent completes the activity in three sections:

1. Selects personality attributes
2. Responds to semantic differential items for the two focus dimensions
3. Reviews or completes general dimension scales

The activity may be presented as:

- One continuous form
- A multi-step wizard
- Collapsible sections
- Separate pages

A multi-step or accordion interface is recommended for clarity.

---

## 10.2 Reviewing Results

After submission, the respondent may see:

- Attribute reflection summary
- Focus dimension scores
- Visual matrix
- Quadrant result
- General dimension scale results
- Reflection prompts
- Optional PDF summary

The result page should state clearly that the output is reflective and should not be treated as a fixed identity.

---

# Result Interpretation

## 11.1 Result Components

The result page may include:

1. Activity title
2. Disclaimer
3. Attribute selections
4. Focus dimension results
5. Matrix visual
6. Matrix quadrant interpretation
7. General dimension results
8. Reflection prompts
9. Optional facilitator notes
10. Optional PDF download

---

## 11.2 Attribute Result

The attribute result should show:

- Attributes selected as self-description, if enabled
- Attributes selected as perceived description by others, if enabled
- Attributes selected in both categories, if enabled
- Optional attribute categories

Example:

```text
Attributes you selected as visible to others:
Courageous, Steadfast, Ordered, Big-picture
```

---

## 11.3 Focus Dimension Result

Each focus dimension should show:

- Dimension label
- Left pole
- Right pole
- Calculated score
- Leaning
- Strength of leaning
- Optional interpretation

Example:

```text
Focus Dimension: Task-oriented ↔ People-oriented
Your responses suggest a moderate leaning toward Task-oriented.
```

This output should be generated dynamically from user-defined labels.

---

## 11.4 Matrix Result

If enabled, the matrix result should show the respondent’s position using the two focus dimensions.

The matrix should use the teacher-defined labels.

Example:

```text
Your matrix result: Organiser / Implementer
```

The quadrant result should display the teacher-defined summary and reflection prompt.

---

## 11.5 General Dimension Result

Each general dimension should show:

- Dimension label
- Respondent’s selected value
- Visual scale marker
- Left and right pole labels
- Optional interpretation

For focus dimensions repeated in this section, the plugin should state whether the value was calculated, adjusted, or both.

Example:

```text
Structured ↔ Unstructured
Calculated from earlier responses: 3 on an 8-point scale
Final reflected position: 4 on an 8-point scale
```

---

# Scoring and Scaling

## 12.1 Basic Scoring

Each semantic differential response is stored as a numeric value from 1 to the number of scale points.

For each focus dimension:

```text
dimension_score = average(scored_item_responses)
```

If an item is reverse-scored:

```text
reversed_score = (scale_points + 1) - original_score
```

---

## 12.2 Normalised Position

For visual display and cross-scale conversion, a score can be converted to a normalised 0–1 position.

```text
normalised_position = (score - 1) / (scale_points - 1)
```

Where:

- 0 = full left pole
- 1 = full right pole

---

## 12.3 Converting Between Scale Lengths

To convert a score from one scale to another:

```text
converted_score = round(normalised_position * (target_scale_points - 1) + 1)
```

Equivalent full formula:

```text
converted_score = round(((raw_score - 1) / (source_scale_points - 1)) * (target_scale_points - 1) + 1)
```

Example:

```text
Source: 4-point average = 2.14
Target: 8-point scale

normalised_position = (2.14 - 1) / (4 - 1)
normalised_position = 0.38

converted_score = round(0.38 * 7 + 1)
converted_score = round(3.66)
converted_score = 4
```

---

## 12.4 Leaning Strength

The plugin may calculate leaning strength based on distance from the centre.

For a normalised position:

- 0.00–0.24 = Strong left pole
- 0.25–0.44 = Moderate left pole
- 0.45–0.55 = Balanced or context-sensitive
- 0.56–0.75 = Moderate right pole
- 0.76–1.00 = Strong right pole

These bands should be configurable.

---

# PDF Output

## 13.1 Purpose

The plugin may optionally generate a PDF summary for respondents.

The PDF should be called a reflection summary rather than a report or assessment certificate.

Suggested heading:

```text
Personality Reflection Summary
```

---

## 13.2 PDF Contents

The PDF may include:

- Respondent name
- Course name
- Activity name
- Date completed
- Disclaimer
- Attribute selections
- Focus dimension results
- Matrix visual or text equivalent
- Matrix quadrant interpretation
- General dimension results
- Reflection prompts
- Optional facilitator notes

---

## 13.3 PDF Disclaimer

Recommended PDF disclaimer:

> This summary is based on self-report reflection responses. It is intended for reflection and conversation, not for diagnosis, selection, or formal psychological assessment.

---

# Privacy and Ethics

## 14.1 Privacy

PersonalityFinder responses may be personally meaningful. The plugin should handle responses respectfully.

The activity creator should clearly explain:

- Who can view respondent results
- Whether results are private
- Whether teachers or facilitators can view individual responses
- Whether data may be exported
- Whether results will be discussed in a group
- Whether PDF summaries may be downloaded

---

## 14.2 Ethical Use

Recommended ethical principles:

- Do not label people rigidly.
- Do not shame respondents for a result.
- Do not compare respondents publicly.
- Do not use results as the only basis for placement or selection.
- Do not treat results as diagnosis.
- Encourage conversation and reflection.
- Allow respondents to disagree with or qualify their results.
- Use results as one input among many.

---

# Accessibility

PersonalityFinder should follow Moodle accessibility expectations.

The plugin should:

- Use semantic form controls
- Label all checkboxes and radio buttons
- Support keyboard navigation
- Avoid relying on colour alone
- Provide readable contrast
- Provide text alternatives for visual matrix results
- Work on mobile screens
- Support screen-reader-friendly output
- Avoid unnecessary animation
- Preserve form state when validation fails

The visual matrix must always be accompanied by a text explanation.

---

# Suggested Moodle Features

## 16.1 Teacher-Facing Features

The teacher or activity creator should be able to:

- Create a new instrument from scratch
- Import a JSON preset
- Export a JSON preset
- Edit all labels and text
- Define personality attributes
- Choose attribute selection mode
- Define exactly two focus dimensions
- Add any number of semantic differential items per focus dimension
- Set scale length for focus dimension items
- Define any number of general dimensions
- Set scale length for general dimensions
- Decide whether focus dimension results appear in the general dimension section
- Decide whether respondents may adjust pre-populated focus dimension values
- Configure the visual matrix
- Define quadrant labels and descriptions
- Preview the instrument
- View individual results, where permitted
- Export results, where permitted
- Enable or disable PDF summaries

---

## 16.2 Respondent-Facing Features

The respondent should be able to:

- Read the introduction and disclaimer
- Complete the attribute section
- Complete focus dimension items
- Complete or review general dimension scales
- See the visual matrix, if enabled
- Read a plain-language result
- See reflection prompts
- Download a PDF summary, if enabled
- Revisit results, if allowed

---

# Suggested Development Priorities

## Phase 1: Core Instrument Builder

- Basic Moodle activity structure
- Activity instance creation
- JSON import/export
- Attribute list management
- Two focus dimensions
- Semantic differential items
- General dimensions
- Basic scoring
- Basic result page

---

## Phase 2: Visual Results

- 2 x 2 matrix visual
- Marker placement
- Quadrant configuration
- Centre-zone handling
- Mobile-friendly display
- Accessible text equivalent

---

## Phase 3: PDF and Reporting

- PDF generation
- Teacher result view
- CSV export
- Improved respondent summary
- Optional facilitator notes

---

## Phase 4: Presets and Reuse

- Built-in example preset
- JSON validation improvements
- Preset library support
- Export/import between Moodle sites
- Versioned schema handling

---

# Example Use Cases

## 17.1 Pastoral S.H.A.P.E. Reflection

A pastor creates an instrument to help church members reflect on personality as one part of ministry discernment.

The two focus dimensions might be:

- Task-oriented ↔ People-oriented
- Structured ↔ Unstructured

The matrix might include:

- Organiser / Implementer
- Starter / Problem-solver
- Shepherd / Facilitator
- Connector / Encourager

The result helps the respondent and pastor discuss possible ministry experiments.

---

## 17.2 Volunteer Placement

A volunteer coordinator creates an instrument to help identify preferred service environments.

The two focus dimensions might be:

- Behind-the-scenes ↔ Public-facing
- Predictable ↔ Flexible

The matrix might suggest broad placement environments such as:

- Reliable support roles
- Flexible response roles
- Public welcome roles
- Community connection roles

---

## 17.3 Educational Reflection

A teacher creates an instrument to help learners reflect on group-work preferences.

The two focus dimensions might be:

- Independent ↔ Collaborative
- Planned ↔ Adaptive

The result helps learners understand how they participate in teams.

---

## 17.4 Recruitment or Staff Development

A recruitment officer or staff development facilitator creates a non-diagnostic reflective instrument to support discussion.

The two focus dimensions might be:

- Analytical ↔ Relational
- Direct ↔ Diplomatic

The result is used as a conversation aid, not as a selection test.

---

# Language Style

PersonalityFinder should use careful, respectful, non-diagnostic language.

Recommended words:

- Reflect
- Suggest
- Indicate
- Preference
- Tendency
- Pattern
- Conversation
- Context
- Growth
- Fit
- Contribution

Words to avoid or use carefully:

- Diagnose
- Prove
- Type
- Defect
- Weakness
- Unsuitable
- Failure
- Abnormal
- Fixed

---

# Summary

PersonalityFinder is a flexible Moodle activity plugin for building personality reflection instruments.

It supports three configurable sections:

1. **Personality Attributes**  
   A user-defined list of attributes selected by the respondent, usually reflecting how they believe others may describe them.

2. **Focus Dimensions**  
   Two user-defined dimensions explored through any number of semantic differential items. These dimensions can be used to produce focused scores and an optional 2 x 2 matrix.

3. **General Dimensions**  
   Any number of additional user-defined bipolar dimensions. The two focus dimensions can be repeated here, with their calculated results converted to the same scale used by the general dimensions.

The plugin’s most important design principle is flexibility. It should not impose a fixed personality model. Instead, it should allow authorised users to build, share, adapt, and reuse reflective instruments through JSON presets.

The intended outcome is not to label people, but to help them reflect wisely on how they tend to relate, serve, decide, organise, and contribute.
