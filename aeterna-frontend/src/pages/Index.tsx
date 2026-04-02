import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import AnimatedSection from "@/components/AnimatedSection";
import PageTransition from "@/components/PageTransition";
import heroImg from "@/assets/hero-1.jpg";
import col1 from "@/assets/collection-1.jpg";
import col2 from "@/assets/collection-2.jpg";
import col3 from "@/assets/collection-3.jpg";

const collections = [
  { img: col1, title: "Autumn Knit", category: "Women" },
  { img: col2, title: "Noir Tailoring", category: "Men" },
  { img: col3, title: "Urban Edge", category: "Streetwear" },
];

const Index = () => (
  <PageTransition>
    {/* Hero */}
    <section className="relative h-screen flex items-center justify-center overflow-hidden">
      <img
        src={heroImg}
        alt="Aeterna fashion campaign"
        className="absolute inset-0 w-full h-full object-cover"
        width={1920}
        height={1080}
      />
      <div className="absolute inset-0 bg-foreground/30" />
      <div className="relative z-10 text-center px-6">
        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 1, delay: 0.3 }}
          className="font-display text-5xl md:text-8xl tracking-[0.15em] uppercase text-primary-foreground mb-6"
        >
          Aeterna
        </motion.h1>
        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 1, delay: 0.6 }}
          className="font-body text-lg md:text-xl tracking-[0.1em] text-primary-foreground/80 mb-10 max-w-lg mx-auto"
        >
          Timeless elegance for the modern soul
        </motion.p>
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 1, delay: 0.9 }}
          className="flex gap-4 justify-center"
        >
          <Link
            to="/collections"
            className="px-8 py-3 bg-primary-foreground text-primary text-sm tracking-[0.15em] uppercase hover:bg-accent hover:text-accent-foreground transition-all duration-300"
          >
            Explore
          </Link>
          <Link
            to="/contact"
            className="px-8 py-3 border border-primary-foreground text-primary-foreground text-sm tracking-[0.15em] uppercase hover:bg-primary-foreground/10 transition-all duration-300"
          >
            Contact
          </Link>
        </motion.div>
      </div>
    </section>

    {/* Featured Collections */}
    <section className="container-luxury py-24 md:py-32">
      <AnimatedSection>
        <h2 className="font-display text-3xl md:text-5xl text-center tracking-[0.1em] uppercase mb-4">
          Featured Collections
        </h2>
        <p className="text-muted-foreground text-center text-sm tracking-wider mb-16 max-w-md mx-auto">
          A curated selection from our latest seasonal offerings
        </p>
      </AnimatedSection>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
        {collections.map((item, i) => (
          <AnimatedSection key={item.title} delay={i * 0.15}>
            <Link to="/collections" className="group block image-zoom">
              <div className="aspect-[3/4] overflow-hidden mb-4">
                <img
                  src={item.img}
                  alt={item.title}
                  className="w-full h-full object-cover"
                  loading="lazy"
                  width={800}
                  height={1100}
                />
              </div>
              <p className="text-xs tracking-[0.2em] uppercase text-muted-foreground mb-1">{item.category}</p>
              <h3 className="font-display text-xl tracking-wider group-hover:text-accent transition-colors">{item.title}</h3>
            </Link>
          </AnimatedSection>
        ))}
      </div>
    </section>

    {/* Brand Statement */}
    <section className="bg-card py-24 md:py-32">
      <div className="container-luxury text-center max-w-3xl mx-auto">
        <AnimatedSection>
          <p className="font-display text-2xl md:text-4xl leading-relaxed tracking-wide text-foreground/90">
            "We believe clothing is not merely worn — it is inhabited. Each piece tells a story of craft, intention, and quiet confidence."
          </p>
          <p className="mt-8 text-xs tracking-[0.3em] uppercase text-muted-foreground">— The Aeterna Philosophy</p>
        </AnimatedSection>
      </div>
    </section>
  </PageTransition>
);

export default Index;
