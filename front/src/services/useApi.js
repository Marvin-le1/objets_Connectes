// useApi.js
import { useState, useCallback } from "react";
import { apiRequest } from "./apiClient";

export function useApi() {
  const [data, setData] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);

  const callApi = useCallback(async (path, options) => {
    setLoading(true);
    setError(null);
    try {
      const result = await apiRequest(path, options);
      setData(result);
      return result;
    } catch (err) {
      setError(err);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  return { data, error, loading, callApi };
}