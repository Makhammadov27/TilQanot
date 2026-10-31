import React, { useMemo, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Alert from '@mui/material/Alert';
import Avatar from '@mui/material/Avatar';
import Badge from '@mui/material/Badge';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Chip from '@mui/material/Chip';
import CssBaseline from '@mui/material/CssBaseline';
import Divider from '@mui/material/Divider';
import LinearProgress from '@mui/material/LinearProgress';
import List from '@mui/material/List';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemText from '@mui/material/ListItemText';
import Paper from '@mui/material/Paper';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import { ThemeProvider, createTheme } from '@mui/material/styles';

const theme = createTheme({
  cssVariables: true,
  palette: {
    mode: 'light',
    primary: { main: '#6548cf', dark: '#4a329f', contrastText: '#fff' },
    secondary: { main: '#bbef69', contrastText: '#24314a' },
    success: { main: '#27856d', contrastText: '#fff' },
    error: { main: '#b45b67', contrastText: '#fff' },
    background: { default: '#f5f3f8', paper: '#fff' },
    text: { primary: '#29243b', secondary: '#716b7e' },
  },
  typography: {
    fontFamily: '"DM Sans", "Segoe UI", sans-serif',
    fontSize: 16,
    h4: { fontFamily: 'Manrope, "DM Sans", sans-serif', fontWeight: 800, letterSpacing: '-0.04em' },
    h5: { fontFamily: 'Manrope, "DM Sans", sans-serif', fontWeight: 800, letterSpacing: '-0.035em' },
    h6: { fontFamily: 'Manrope, "DM Sans", sans-serif', fontWeight: 750, letterSpacing: '-0.025em' },
    button: { fontWeight: 700, textTransform: 'none' },
  },
  shape: { borderRadius: 16 },
  components: {
    MuiButton: { styleOverrides: { root: { minHeight: 46, borderRadius: 12, paddingInline: 18, boxShadow: 'none' } } },
    MuiCard: { styleOverrides: { root: { border: '1px solid #e9e5f0', boxShadow: '0 8px 28px rgba(47, 35, 79, .055)' } } },
    MuiListItemButton: { styleOverrides: { root: { minHeight: 54, borderRadius: 11, marginBlock: 3 } } },
  },
});

function readJson(id, fallback) {
  try { return JSON.parse(document.getElementById(id)?.textContent || 'null') ?? fallback; }
  catch { return fallback; }
}

function LearningPage({ root, words, lessons }) {
  const [index, setIndex] = useState(0);
  const [revealed, setRevealed] = useState(false);
  const start = Number(root.dataset.start || 0);
  const lessonId = Number(root.dataset.lessonId || 0);
  const token = root.dataset.token || '';
  const current = words[index];
  const duePosition = words.length ? (start + index) % words.length : 0;

  const move = (delta) => {
    if (!words.length) return;
    setIndex((value) => (value + delta + words.length) % words.length);
    setRevealed(false);
  };

  if (!words.length) {
    return <Alert severity="success" sx={{ borderRadius: 4, p: 3, fontSize: 16 }}>
      <Typography variant="h6" sx={{ mb: 0.5 }}>Bugungi takrorlashlar tugadi</Typography>
      Hozircha qayta ko‘rish vaqti kelgan so‘zlar yo‘q. Yangi dars tanlashingiz mumkin.
    </Alert>;
  }

  return <Box sx={{ width: '100%' }}>
    <Stack direction={{ xs: 'column', sm: 'row' }} justifyContent="space-between" alignItems={{ xs: 'flex-start', sm: 'center' }} spacing={2} sx={{ mb: 3 }}>
      <Box>
        <Typography component="div" sx={{ color: 'primary.main', fontSize: 12, fontWeight: 800, letterSpacing: '.15em', textTransform: 'uppercase', mb: 0.75 }}>KUNLIK AMALIYOT</Typography>
        <Typography variant="h4" sx={{ fontSize: { xs: 30, md: 38 } }}>So‘zlarni mustahkamlang</Typography>
        <Typography sx={{ color: 'text.secondary', mt: 0.75, fontSize: 16 }}>Eslab ko‘ring, keyin javobni oching.</Typography>
      </Box>
      <Button href="index.php" variant="outlined" color="inherit" sx={{ bgcolor: 'background.paper', borderColor: '#ded8e9', height: 46, alignSelf: 'flex-start', flexShrink: 0 }}>← Bosh sahifa</Button>
    </Stack>

    <Box sx={{ display: 'grid', gridTemplateColumns: { xs: '1fr', lg: '288px minmax(0,1fr)' }, gap: 2.5, alignItems: 'start' }}>
      <Card sx={{ borderRadius: 4, overflow: 'hidden' }}>
        <CardContent sx={{ p: { xs: 2, sm: 2.5 } }}>
          <Stack direction="row" alignItems="center" spacing={1.5} sx={{ mb: 1 }}>
            <Avatar sx={{ bgcolor: '#eee9ff', color: 'primary.main', width: 40, height: 40 }}>✦</Avatar>
            <Box><Typography variant="h6" sx={{ fontSize: 18 }}>Darslar</Typography><Typography sx={{ color: 'text.secondary', fontSize: 13 }}>Mavzuni tanlang</Typography></Box>
          </Stack>
          <List disablePadding sx={{ mt: 1.5 }}>
            <ListItemButton component="a" href="index.php?route=learn" selected={!lessonId} sx={{ '&.Mui-selected': { bgcolor: '#eee9ff', color: 'primary.dark' } }}>
              <ListItemText primary="Barcha so‘zlar" primaryTypographyProps={{ fontSize: 14, fontWeight: 700 }} />
              <Badge color="primary" badgeContent={words.length} max={999} sx={{ mr: 1.5 }} />
            </ListItemButton>
            {lessons.map((lesson) => <ListItemButton key={lesson.id} component="a" href={`index.php?route=learn&lesson=${lesson.id}`} selected={Number(lesson.id) === lessonId} sx={{ alignItems: 'flex-start', '&.Mui-selected': { bgcolor: '#eee9ff', color: 'primary.dark' } }}>
              <ListItemText primary={lesson.title} secondary={lesson.course_title} primaryTypographyProps={{ sx: { fontSize: 14, fontWeight: 650, lineHeight: 1.35 } }} secondaryTypographyProps={{ sx: { fontSize: 12, mt: 0.25 } }} />
              <Typography sx={{ fontSize: 12, color: 'text.secondary', pt: 0.5 }}>{lesson.words_count}</Typography>
            </ListItemButton>)}
          </List>
          <Divider sx={{ my: 2 }} />
          <Paper variant="outlined" sx={{ p: 1.75, bgcolor: '#faf8ff', borderColor: '#eee8fa', borderRadius: 3 }}>
            <Stack direction="row" spacing={1} alignItems="center" sx={{ mb: 0.5 }}><Typography sx={{ color: 'primary.main' }}>✦</Typography><Typography sx={{ fontWeight: 750, fontSize: 14 }}>Yodlash usuli</Typography></Stack>
            <Typography sx={{ color: 'text.secondary', fontSize: 13, lineHeight: 1.55 }}>Tarjimani ochishdan oldin javobni xotiradan aytishga urinib ko‘ring.</Typography>
          </Paper>
        </CardContent>
      </Card>

      <Card sx={{ borderRadius: 4, overflow: 'hidden' }}>
        <CardContent sx={{ p: { xs: 2, sm: 3, lg: 3.5 } }}>
          <Stack direction={{ xs: 'column', sm: 'row' }} spacing={1.5} alignItems={{ xs: 'stretch', sm: 'center' }} sx={{ mb: 2.5 }}>
            <Typography sx={{ fontWeight: 750, whiteSpace: 'nowrap', fontSize: 14 }}>So‘z {duePosition + 1} / {words.length}</Typography>
            <LinearProgress variant="determinate" value={((duePosition + 1) / words.length) * 100} sx={{ flex: 1, height: 8, borderRadius: 9, bgcolor: '#ebe7f1', '& .MuiLinearProgress-bar': { borderRadius: 9 } }} />
            <Chip label={current.course_title} color="primary" variant="outlined" sx={{ alignSelf: { xs: 'flex-start', sm: 'center' }, fontWeight: 700, maxWidth: '100%' }} />
          </Stack>

          <Paper variant="outlined" sx={{ minHeight: { xs: 360, sm: 420 }, borderRadius: 4, p: { xs: 2.5, sm: 4 }, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', position: 'relative', overflow: 'hidden', background: 'radial-gradient(circle at 90% 8%, #eee8ff 0, transparent 30%), linear-gradient(145deg,#fff,#fbfaff)', borderColor: '#e7e1f0' }}>
            <Chip label={current.lesson_title} sx={{ position: 'absolute', top: 20, left: 20, maxWidth: 'calc(100% - 40px)', bgcolor: '#eee9ff', color: 'primary.dark', fontWeight: 700 }} />
            <Box sx={{ color: 'primary.main', fontSize: 26, lineHeight: 1, mb: 1 }}>◖)))</Box>
            <Typography variant="h2" sx={{ fontSize: { xs: 44, sm: 60 }, fontWeight: 850, letterSpacing: '-.055em', lineHeight: 1.1, overflowWrap: 'anywhere' }}>{current.term}</Typography>
            {current.pronunciation && <Typography sx={{ mt: 1.25, color: 'text.secondary', fontSize: 16 }}>/ {current.pronunciation} /</Typography>}

            {revealed ? <Box sx={{ mt: 2.5, animation: 'rise .2s ease' }}>
              <Typography variant="h5" sx={{ color: 'primary.dark', fontSize: { xs: 24, sm: 30 } }}>{current.translation}</Typography>
              {current.example_sentence && <Typography sx={{ mt: 1, color: 'text.secondary', fontSize: 15 }}>{current.example_sentence}</Typography>}
            </Box> : <Button onClick={() => setRevealed(true)} variant="contained" sx={{ mt: 3 }}>Tarjimani ko‘rish <span style={{ marginLeft: 8 }}>⌄</span></Button>}

            {revealed && <Stack direction={{ xs: 'column', sm: 'row' }} spacing={1.5} sx={{ mt: 3, width: { xs: '100%', sm: 'auto' } }}>
              <form method="post" action="index.php?route=learn">
                <input type="hidden" name="_token" value={token} /><input type="hidden" name="vocabulary_id" value={current.id} /><input type="hidden" name="lesson_id" value={lessonId} /><input type="hidden" name="cursor" value={duePosition} /><input type="hidden" name="result" value="again" />
                <Button type="submit" variant="outlined" color="error" fullWidth>↻ Yana takrorlash</Button>
              </form>
              <form method="post" action="index.php?route=learn">
                <input type="hidden" name="_token" value={token} /><input type="hidden" name="vocabulary_id" value={current.id} /><input type="hidden" name="lesson_id" value={lessonId} /><input type="hidden" name="cursor" value={duePosition} /><input type="hidden" name="result" value="known" />
                <Button type="submit" variant="contained" color="success" fullWidth>✓ Bilaman</Button>
              </form>
            </Stack>}
          </Paper>

          <Stack direction="row" justifyContent="space-between" alignItems="center" sx={{ mt: 2.25 }}>
            <Button variant="outlined" color="inherit" disabled={words.length < 2} onClick={() => move(-1)}>← Oldingi</Button>
            <Typography sx={{ color: 'text.secondary', fontSize: 13, display: { xs: 'none', sm: 'block' } }}>Javobingizni belgilang va davom eting</Typography>
            <Button variant="outlined" color="inherit" disabled={words.length < 2} onClick={() => move(1)}>Keyingisi →</Button>
          </Stack>
        </CardContent>
      </Card>
    </Box>
  </Box>;
}

const root = document.getElementById('mui-learning-root');
if (root) {
  const words = readJson('word-data', []);
  const lessons = readJson('lesson-data', []);
  createRoot(root).render(<ThemeProvider theme={theme}><CssBaseline /><LearningPage root={root} words={words} lessons={lessons} /></ThemeProvider>);
}
