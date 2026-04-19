<div class="ptf-bar">
  <div class="ptf-inner">
    <span class="ptf-left">Secure payments powered by Stripe</span>
    <div class="ptf-icons" aria-label="Accepted payment methods">
      <span class="ptf-icon" title="Stripe">
        <svg width="40" height="24" viewBox="0 0 40 24" fill="none" aria-hidden="true">
          <rect x="4" y="1" width="32" height="22" rx="3.5" stroke="currentColor" stroke-width="1"/>
          <text x="20" y="17" font-size="12" text-anchor="middle" fill="currentColor" font-family="DM Sans,sans-serif" font-weight="300">S</text>
        </svg>
        <span class="ptf-icon-label">Stripe</span>
      </span>
      <span class="ptf-icon" title="Card">
        <svg width="40" height="24" viewBox="0 0 40 24" fill="none" aria-hidden="true">
          <rect x="4" y="1" width="32" height="22" rx="3.5" stroke="currentColor" stroke-width="1"/>
          <rect x="4" y="8" width="32" height="5" fill="currentColor" opacity=".18"/>
          <rect x="8" y="16" width="9" height="3" rx="1" fill="currentColor" opacity=".4"/>
        </svg>
        <span class="ptf-icon-label">Card</span>
      </span>
      <span class="ptf-icon" title="USD Coin">
        <svg width="40" height="24" viewBox="0 0 40 24" fill="none" aria-hidden="true">
          <circle cx="20" cy="12" r="10" stroke="currentColor" stroke-width="1"/>
          <text x="20" y="16" font-size="7" text-anchor="middle" fill="currentColor" font-family="DM Sans,sans-serif" font-weight="400">USDC</text>
        </svg>
        <span class="ptf-icon-label">USDC</span>
      </span>
      <span class="ptf-icon" title="Ethereum">
        <svg width="40" height="24" viewBox="0 0 40 24" fill="none" aria-hidden="true">
          <polygon points="20,2 30,12 20,16 10,12" stroke="currentColor" stroke-width="1" stroke-linejoin="round"/>
          <polygon points="20,16 30,12 20,22 10,12" stroke="currentColor" stroke-width="1" stroke-linejoin="round" opacity=".5"/>
        </svg>
        <span class="ptf-icon-label">ETH</span>
      </span>
      <span class="ptf-icon" title="Bitcoin">
        <svg width="40" height="24" viewBox="0 0 40 24" fill="none" aria-hidden="true">
          <text x="20" y="19" font-size="17" text-anchor="middle" fill="currentColor" font-family="serif">&#x20BF;</text>
        </svg>
        <span class="ptf-icon-label">BTC</span>
      </span>
    </div>
    <span class="ptf-note">Crypto-ready. Modern payment infrastructure.</span>
  </div>
</div>

<style>
.ptf-bar{
  position:relative;
  padding:20px 32px 18px;
  border-top:1px solid rgba(200,168,75,.10);
  background:linear-gradient(180deg,rgba(200,168,75,.015) 0%,transparent 100%);
  margin-bottom:0;
}
.ptf-bar::before{
  content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);
  width:min(280px,60%);height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.22),transparent);
}
.ptf-inner{
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
  max-width:var(--wrap-max,1280px);margin:0 auto;
}
.ptf-left{
  font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;
  color:rgba(168,168,160,.46);
}
.ptf-note{
  font-size:.62rem;letter-spacing:.08em;
  color:rgba(168,168,160,.34);font-style:italic;
}
.ptf-icons{
  display:flex;align-items:center;gap:22px;
}
.ptf-icon{
  display:flex;flex-direction:column;align-items:center;gap:4px;
  opacity:.42;transition:opacity .25s,transform .2s;cursor:default;
  color:var(--ivory, rgba(237,232,222,.9));
}
.ptf-icon:hover{opacity:.78;transform:translateY(-1px)}
.ptf-icon-label{
  font-size:.54rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(168,168,160,.58);
}
@media(max-width:600px){
  .ptf-bar{padding:16px 20px 14px}
  .ptf-inner{flex-direction:column;align-items:center;text-align:center;gap:10px}
  .ptf-note{display:none}
  .ptf-icons{gap:16px}
}
</style>
