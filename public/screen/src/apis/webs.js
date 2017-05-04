import { WEBS_API_ENDPOINT } from '../strings';
import * as fromRest from '../util/rest';
import { getBase, getSeparator } from './base';

// eslint-disable-next-line
export const get = (playlistIds) => {
  const version = Date.now();
// eslint-disable-next-line
  return fromRest.get(`${getBase()}${WEBS_API_ENDPOINT}${getSeparator()}fs-playlists=${playlistIds.join(',')}&version=${version}`)
  .then(response => (response.map(o => ({
    id: o.id,
    url: o.acf.url,
    webDuration: Number(o.acf.web_duration),
    priority: o.acf.priority !== undefined ? Number(o.acf.priority) : 1,
  }))))
  .catch(() => ([]));
};