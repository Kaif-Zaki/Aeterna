import { Link } from "react-router-dom";
import { Instagram, Twitter } from "lucide-react";

const Footer = () => (
  <footer className="border-t border-border bg-card py-16 mt-24">
    <div className="container-luxury">
      <div className="grid grid-cols-1 md:grid-cols-4 gap-12">
        <div className="md:col-span-2">
          <h3 className="font-display text-2xl tracking-[0.2em] uppercase mb-4">Aeterna</h3>
          <p className="text-muted-foreground text-sm leading-relaxed max-w-sm">
            Timeless elegance, modern sensibility. Crafted for those who appreciate the art of dressing.
          </p>
        </div>
        <div>
          <h4 className="text-xs tracking-[0.2em] uppercase mb-4 text-muted-foreground">Navigate</h4>
          <div className="flex flex-col gap-2">
            {["/about", "/collections", "/gallery", "/contact", "/faq"].map((path) => (
              <Link key={path} to={path} className="text-sm text-foreground/70 hover:text-accent transition-colors capitalize">
                {path.slice(1)}
              </Link>
            ))}
          </div>
        </div>
        <div>
          <h4 className="text-xs tracking-[0.2em] uppercase mb-4 text-muted-foreground">Follow</h4>
          <div className="flex gap-4">
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" className="text-foreground/70 hover:text-accent transition-colors">
              <Instagram size={20} />
            </a>
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" className="text-foreground/70 hover:text-accent transition-colors">
              <Twitter size={20} />
            </a>
          </div>
        </div>
      </div>
      <div className="mt-16 pt-8 border-t border-border text-center">
        <p className="text-xs text-muted-foreground tracking-widest uppercase">
          © {new Date().getFullYear()} Aeterna. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
);

export default Footer;
