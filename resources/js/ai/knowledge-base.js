/**
 * AI Citation Knowledge Base
 * Structured terminology and upsell intelligence for AI visibility scoring.
 */

export const knowledgeBase = {
  structured_data_layer: {
    title: 'Structured Data Layer',
    explanation: 'Machine-readable markup (JSON-LD, Schema.org) that tells AI systems who you are, what you do, and where you operate.',
    why_it_matters: 'AI citation engines prefer sources that explicitly declare their identity and service structure. Missing schema forces the AI to guess — and it often guesses wrong.',
    leads_to: 'Higher confidence extraction, reduced citation skips, better entity recognition.',
  },

  entity_authority: {
    title: 'Entity Authority',
    explanation: 'The degree to which your site is treated as a reliable, named source on a specific topic or service domain.',
    why_it_matters: 'AI systems rank extractable entities. Without clear entity signals, your content is treated as anonymous commodity, not a citable source.',
    leads_to: 'Improved citation selection rate, stronger brand presence in AI-generated answers.',
  },

  citation_signals: {
    title: 'Citation Signals',
    explanation: 'Structural and semantic markers that signal to AI that your content is answer-grade: direct phrasing, clear attributions, question-response format.',
    why_it_matters: 'AI citation engines scan for content that answers questions cleanly. Weak citation signals mean your content is parsed but not selected.',
    leads_to: 'Higher selection rate in AI-generated responses, improved topical coverage.',
  },

  content_connectivity: {
    title: 'Content Connectivity',
    explanation: 'The internal linking architecture that connects related pages so AI can trace the full depth and breadth of your topic coverage.',
    why_it_matters: 'AI systems map content graphs. Disconnected pages reduce perceived authority. Tight link clusters signal topical mastery.',
    leads_to: 'Stronger topical cluster signals, improved co-citation probability.',
  },

  topic_depth: {
    title: 'Topic Depth',
    explanation: 'The richness and specificity of coverage across your core service or subject areas — measured against AI extraction thresholds.',
    why_it_matters: 'Shallow pages fail extraction depth tests. AI systems require sufficient signal density to prefer your content over alternatives.',
    leads_to: 'Reduced extraction failures, higher selection confidence for long-tail AI queries.',
  },
};

/**
 * Upsell logic based on AI visibility score.
 * Returns the most relevant tier, price, and plan slug for a given score.
 *
 * @param {number} score — AI visibility score 0–100
 * @returns {{ tier: string, price: string, plan: string, cta: string }}
 */
export const upsellLogic = (score) => {
  if (score <= 40) {
    return {
      tier: 'Fix Strategy',
      price: '$249',
      plan: 'fix-strategy',
      cta: 'Get My Fix Plan',
    };
  }
  if (score <= 70) {
    return {
      tier: 'Signal Expansion',
      price: '$99',
      plan: 'diagnostic',
      cta: 'Expand My Signal Coverage',
    };
  }
  return {
    tier: 'System Activation',
    price: '$489',
    plan: 'optimization',
    cta: 'Activate Full System Intelligence',
  };
};

/**
 * Returns the badge label and CSS modifier class for a given score.
 *
 * @param {number} score
 * @returns {{ label: string, cls: string }}
 */
export const scoreBadge = (score) => {
  if (score >= 71) return { label: 'Above Baseline', cls: 'above' };
  if (score >= 41) return { label: 'Emerging',       cls: 'emerging' };
  return                  { label: 'At Risk',        cls: 'risk' };
};
