import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import AnimatedSection from "@/components/AnimatedSection";
import PageTransition from "@/components/PageTransition";
import { X } from "lucide-react";
import col1 from "@/assets/collection-1.jpg";
import col2 from "@/assets/collection-2.jpg";
import col3 from "@/assets/collection-3.jpg";
import col4 from "@/assets/collection-4.jpg";
import col5 from "@/assets/collection-5.jpg";
import col6 from "@/assets/collection-6.jpg";

const items = [
  { id: 1, img: col1, title: "Cashmere Turtleneck", category: "Women", desc: "Premium cashmere in warm beige. Relaxed fit with ribbed detailing." },
  { id: 2, img: col2, title: "Noir Tailored Suit", category: "Men", desc: "Sharp tailoring in midnight black. Italian wool, slim fit." },
  { id: 3, img: col3, title: "Urban Hoodie Set", category: "Streetwear", desc: "Oversized cotton hoodie with matching joggers. Earthy rust tone." },
  { id: 4, img: col4, title: "Silk Flow Dress", category: "Women", desc: "Flowing ivory silk with delicate draping. Floor-length elegance." },
  { id: 5, img: col5, title: "Knit Wrap Cardigan", category: "Women", desc: "Chunky ribbed knit with belted waist. Cream and oat tones." },
  { id: 6, img: col6, title: "Linen Resort Set", category: "Women", desc: "Light linen shirt and trousers. Perfect for warm-weather ease." },
];

const categories = ["All", "Women", "Men", "Streetwear"];

const CollectionsPage = () => {
  const [filter, setFilter] = useState("All");
  const [selected, setSelected] = useState<typeof items[0] | null>(null);

  const filtered = filter === "All" ? items : items.filter((i) => i.category === filter);

  return (
    <PageTransition>
      <div className="pt-28 pb-16">
        <section className="container-luxury">
          <AnimatedSection>
            <h1 className="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4">Collections</h1>
            <p className="text-muted-foreground text-sm tracking-wider mb-12">Explore our curated lookbook</p>
          </AnimatedSection>

          {/* Filters */}
          <AnimatedSection>
            <div className="flex gap-6 mb-12 flex-wrap">
              {categories.map((cat) => (
                <button
                  key={cat}
                  onClick={() => setFilter(cat)}
                  className={`text-xs tracking-[0.2em] uppercase pb-1 border-b-2 transition-colors ${
                    filter === cat ? "border-accent text-foreground" : "border-transparent text-muted-foreground hover:text-foreground"
                  }`}
                >
                  {cat}
                </button>
              ))}
            </div>
          </AnimatedSection>

          {/* Grid */}
          <motion.div layout className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <AnimatePresence mode="popLayout">
              {filtered.map((item, i) => (
                <motion.div
                  key={item.id}
                  layout
                  initial={{ opacity: 0, scale: 0.95 }}
                  animate={{ opacity: 1, scale: 1 }}
                  exit={{ opacity: 0, scale: 0.95 }}
                  transition={{ duration: 0.4, delay: i * 0.05 }}
                >
                  <button onClick={() => setSelected(item)} className="w-full text-left group image-zoom">
                    <div className="aspect-[3/4] overflow-hidden mb-4">
                      <img src={item.img} alt={item.title} className="w-full h-full object-cover" loading="lazy" width={800} height={1100} />
                    </div>
                    <p className="text-xs tracking-[0.2em] uppercase text-muted-foreground mb-1">{item.category}</p>
                    <h3 className="font-display text-lg tracking-wider group-hover:text-accent transition-colors">{item.title}</h3>
                  </button>
                </motion.div>
              ))}
            </AnimatePresence>
          </motion.div>
        </section>

        {/* Modal */}
        <AnimatePresence>
          {selected && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="fixed inset-0 z-50 bg-foreground/60 backdrop-blur-sm flex items-center justify-center p-6"
              onClick={() => setSelected(null)}
            >
              <motion.div
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.9 }}
                className="bg-background max-w-3xl w-full grid grid-cols-1 md:grid-cols-2 overflow-hidden relative"
                onClick={(e) => e.stopPropagation()}
              >
                <button
                  onClick={() => setSelected(null)}
                  className="absolute top-4 right-4 z-10 p-2 hover:bg-secondary rounded-full transition-colors"
                >
                  <X size={20} />
                </button>
                <div className="aspect-[3/4] md:aspect-auto">
                  <img src={selected.img} alt={selected.title} className="w-full h-full object-cover" />
                </div>
                <div className="p-8 md:p-12 flex flex-col justify-center">
                  <p className="text-xs tracking-[0.3em] uppercase text-muted-foreground mb-3">{selected.category}</p>
                  <h2 className="font-display text-2xl md:text-3xl tracking-wider mb-4">{selected.title}</h2>
                  <p className="text-muted-foreground leading-relaxed">{selected.desc}</p>
                </div>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </PageTransition>
  );
};

export default CollectionsPage;
