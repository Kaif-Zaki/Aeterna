<?php declare(strict_types=1); ?>
  <footer class="border-t border-border bg-card py-16 mt-24">
    <div class="container-luxury">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="md:col-span-2">
          <h3 class="font-display text-2xl tracking-[0.2em] uppercase mb-4">Aeterna</h3>
          <p class="text-muted-foreground text-sm leading-relaxed max-w-sm">
            Timeless elegance, modern sensibility. Crafted for those who appreciate the art of dressing.
          </p>
        </div>
        <div>
          <h4 class="text-xs tracking-[0.2em] uppercase mb-4 text-muted-foreground">Navigate</h4>
          <div class="flex flex-col gap-2">
            <a href="/about.php" class="text-sm text-foreground/70 hover:text-accent transition-colors">about</a>
            <a href="/collections.php" class="text-sm text-foreground/70 hover:text-accent transition-colors">collections</a>
            <a href="/gallery.php" class="text-sm text-foreground/70 hover:text-accent transition-colors">gallery</a>
            <a href="/contact.php" class="text-sm text-foreground/70 hover:text-accent transition-colors">contact</a>
            <a href="/faq.php" class="text-sm text-foreground/70 hover:text-accent transition-colors">faq</a>
            <a href="/admin/login.php" class="text-sm text-foreground/70 hover:text-accent transition-colors">admin login</a>
          </div>
        </div>
        <div>
          <h4 class="text-xs tracking-[0.2em] uppercase mb-4 text-muted-foreground">Follow</h4>
          <div class="flex flex-col gap-2">
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="text-foreground/70 hover:text-accent transition-colors">Instagram</a>
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="text-foreground/70 hover:text-accent transition-colors">Twitter</a>
          </div>
        </div>
      </div>
      <div class="mt-16 pt-8 border-t border-border text-center">
        <p class="text-xs text-muted-foreground tracking-widest uppercase">
          &copy; <?= date('Y') ?> Aeterna. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
  <script src="/assets/site.js"></script>
  <script src="/assets/scroll-animations.js"></script>
</body>
</html>
