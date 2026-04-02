import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import AnimatedSection from "@/components/AnimatedSection";
import PageTransition from "@/components/PageTransition";
import { X, ChevronLeft, ChevronRight } from "lucide-react";
import g1 from "@/assets/gallery-1.jpg";
import g2 from "@/assets/gallery-2.jpg";
import g3 from "@/assets/gallery-3.jpg";
import g4 from "@/assets/gallery-4.jpg";
import col4 from "@/assets/collection-4.jpg";
import heroImg from "@/assets/hero-1.jpg";

const images = [
  { src: g1, alt: "Campaign — Duo in Noir", span: "row-span-2" },
  { src: g2, alt: "Campaign — Desert Walk", span: "" },
  { src: g3, alt: "Detail — Timepiece", span: "" },
  { src: g4, alt: "Portrait — Minimalist", span: "row-span-2" },
  { src: col4, alt: "Silk in Motion", span: "" },
  { src: heroImg, alt: "Architectural Light", span: "" },
];

const GalleryPage = () => {
  const [lightbox, setLightbox] = useState<number | null>(null);

  const navigate = (dir: number) => {
    if (lightbox === null) return;
    setLightbox((lightbox + dir + images.length) % images.length);
  };

  return (
    <PageTransition>
      <div className="pt-28 pb-16">
        <section className="container-luxury">
          <AnimatedSection>
            <h1 className="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4">Campaigns</h1>
            <p className="text-muted-foreground text-sm tracking-wider mb-12">A visual narrative of our brand</p>
          </AnimatedSection>

          <div className="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 auto-rows-[250px] md:auto-rows-[300px]">
            {images.map((img, i) => (
              <AnimatedSection key={i} delay={i * 0.08} className={img.span}>
                <button
                  onClick={() => setLightbox(i)}
                  className="w-full h-full image-zoom block"
                >
                  <img
                    src={img.src}
                    alt={img.alt}
                    className="w-full h-full object-cover"
                    loading="lazy"
                  />
                </button>
              </AnimatedSection>
            ))}
          </div>
        </section>

        {/* Lightbox */}
        <AnimatePresence>
          {lightbox !== null && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="fixed inset-0 z-50 bg-foreground/90 flex items-center justify-center"
              onClick={() => setLightbox(null)}
            >
              <button onClick={() => setLightbox(null)} className="absolute top-6 right-6 text-primary-foreground">
                <X size={28} />
              </button>
              <button
                onClick={(e) => { e.stopPropagation(); navigate(-1); }}
                className="absolute left-4 md:left-8 text-primary-foreground hover:text-accent transition-colors"
              >
                <ChevronLeft size={36} />
              </button>
              <motion.img
                key={lightbox}
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.9 }}
                src={images[lightbox].src}
                alt={images[lightbox].alt}
                className="max-h-[85vh] max-w-[90vw] object-contain"
                onClick={(e) => e.stopPropagation()}
              />
              <button
                onClick={(e) => { e.stopPropagation(); navigate(1); }}
                className="absolute right-4 md:right-8 text-primary-foreground hover:text-accent transition-colors"
              >
                <ChevronRight size={36} />
              </button>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </PageTransition>
  );
};

export default GalleryPage;
