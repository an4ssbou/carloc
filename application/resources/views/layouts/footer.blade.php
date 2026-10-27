@if(config('demo.enabled'))
<div class="max-w-7xl mx-auto px-6 text-sm text-slate-300"><strong>CarLoc — démonstration de portfolio.</strong> Données fictives partagées, aucune offre de location réelle. Aucun contact, inscription ou paiement collecté.</div>
@else

    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 max-w-7xl mx-auto px-6">
        <div>
            <span class="text-xl font-black text-white mb-4 block">CarLoc</span>
            <p class="text-slate-400 text-sm leading-relaxed mb-6">The premier marketplace for car rentals in Morocco. Quality vehicles from trusted local agencies.</p>
            <div class="flex gap-4">
                <div class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center text-white hover:text-secondary-container transition-colors cursor-pointer"><span class="material-symbols-outlined">share</span></div>
                <div class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center text-white hover:text-secondary-container transition-colors cursor-pointer"><span class="material-symbols-outlined">public</span></div>
            </div>
        </div>
        <div>
            <h5 class="text-white font-bold mb-6">Quick Links</h5>
            <ul class="space-y-4 text-slate-400 text-sm">
                <li><a class="hover:text-white transition-colors duration-200" href="#">Browse Vehicles</a></li>
                <li><a class="hover:text-white transition-colors duration-200" href="#">Partner Agencies</a></li>
                <li><a class="hover:text-white transition-colors duration-200" href="#">List Your Car</a></li>
                <li><a class="hover:text-white transition-colors duration-200" href="#">How It Works</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-bold mb-6">Support</h5>
            <ul class="space-y-4 text-slate-400 text-sm">
                <li><a class="hover:text-white transition-colors duration-200" href="#">Contact Support</a></li>
                <li><a class="hover:text-white transition-colors duration-200" href="#">FAQs</a></li>
                <li><a class="hover:text-white transition-colors duration-200" href="#">Privacy Policy</a></li>
                <li><a class="hover:text-white transition-colors duration-200" href="#">Terms of Service</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-bold mb-6">Newsletter</h5>
            <p class="text-slate-400 text-sm mb-4">Subscribe for special offers and travel guides.</p>
            <div class="flex">
                <input class="bg-white/5 border-none rounded-l-md px-4 py-2 text-white w-full focus:ring-1 focus:ring-secondary-container" placeholder="Email address" type="email"/>
                <button class="bg-secondary-container text-on-secondary-container px-4 py-2 rounded-r-md">
                    <span class="material-symbols-outlined">send</span>
                </button>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-6 mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-500 text-sm">
        <p>© 2026 CarLoc Morocco. All rights reserved.</p>
        <div class="flex gap-8">
            <span>Oujda, Morocco</span>
            <span>+212 762 73 27 30</span>
        </div>
    </div>

@endif
