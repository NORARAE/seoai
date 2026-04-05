<div class="ptf-bar">
  <span class="ptf-left">Secure payments powered by Stripe</span>
  <div class="ptf-icons" aria-label="Accepted payment methods">
    <span class="ptf-icon" title="Stripe">
      <svg width="36" height="22" viewBox="0 0 36 22" fill="none" aria-hidden="true">
        <rect x="4" y="1" width="28" height="20" rx="3" stroke="currentColor" stroke-width="1"/>
        <text x="18" y="16" font-size="11" text-anchor="middle" fill="currentColor" font-family="DM Sans,sans-serif" font-weight="300">S</text>
      </svg>
      <span class="ptf-icon-label">Stripe</span>
    </span>
    <span class="ptf-icon" title="Card">
      <svg width="36" height="22" viewBox="0 0 36 22" fill="none" aria-hidden="true">
        <rect x="4" y="1" width="28" height="20" rx="3" stroke="currentColor" stroke-width="1"/>
        <rect x="4" y="7" width="28" height="5" fill="currentColor" opacity=".18"/>
        <rect x="8" y="15" width="8" height="3" rx="1" fill="currentColor" opacity=".4"/>
      </svg>
      <span class="ptf-icon-label">Card</span>
    </span>
    <span class="ptf-icon" title="USD Coin">
      <svg width="36" height="22" viewBox="0 0 36 22" fill="none" aria-hidden="true">
        <circle cx="18" cy="11" r="9" stroke="currentColor" stroke-width="1"/>
        <text x="18" y="15" font-size="6.5" text-anchor="middle" fill="currentColor" font-family="DM Sans,sans-serif" font-weight="400">USDC</text>
      </svg>
      <span class="ptf-icon-label">USDC</span>
    </span>
    <span class="ptf-icon" title="Ethereum">
      <svg width="36" height="22" viewBox="0 0 36 22" fill="none" aria-hidden="true">
        <polygon points="18,2 28,11 18,15 8,11" stroke="currentColor" stroke-width="1" stroke-linejoin="round"/>
        <polygon points="18,15 28,11 18,21 8,11" stroke="currentColor" stroke-width="1" stroke-linejoin="round" opacity=".5"/>
      </svg>
      <span class="ptf-icon-label">ETH</span>
    </span>
    <span class="ptf-icon" title="Bitcoin">
      <svg width="36" height="22" viewBox="0 0 36 22" fill="none" aria-hidden="true">
        <text x="18" y="17" font-size="16" text-anchor="middle" fill="currentColor" font-family="serif">&#x20BF;</text>
      </svg>
      <span class="ptf-icon-label">BTC</span>
    </span>
  </div>
  <span class="ptf-note">Crypto-ready. Modern payment support.</span>
</div>

<style>
.ptf-bar{
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;
  padding:14px 32px;
  border-top:1px solid rgba(200,168,75,.07);
  margin-bottom:4px;
}
.ptf-left{
  font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;
  color:rgba(168,168,160,.32);
}
.ptf-note{
  font-size:.58rem;letter-spacing:.08em;
  color:rgba(168,168,160,.22);font-style:italic;
}
.ptf-icons{
  display:flex;align-items:center;gap:18px;
}
.ptf-icon{
  display:flex;flex-direction:column;align-items:center;gap:3px;
  opacity:.28;transition:opacity .25s;cursor:default;
  color:var(--ivory, rgba(237,232,222,.9));
}
.ptf-icon:hover{opacity:.7;}
.ptf-icon-label{
  font-size:.46rem;letter-spacing:.16em;text-transform:uppercase;
  color:rgba(168,168,160,.5);
}
@media(max-width:600px){
  .ptf-bar{flex-direction:column;align-items:center;text-align:center;gap:8px;padding:14px 20px}
  .ptf-note{display:none}
}
</style>
